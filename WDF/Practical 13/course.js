
const courseContainer = document.getElementById("courseContainer");
const searchInput = document.getElementById("searchCourse");
const message = document.getElementById("courseMessage");
const form = document.getElementById("courseForm");
const courseId = document.getElementById("courseId");
const courseName = document.getElementById("courseName");
const courseDescription = document.getElementById("courseDescription");
const courseIcon = document.getElementById("courseIcon");
const formHeading = document.getElementById("formHeading");
const cancelEdit = document.getElementById("cancelEdit");

let courses = [];

async function apiRequest(method, data = null) {
    const options = {
        method,
        headers: {}
    };

    if (data !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    const response = await fetch("course-api.php", options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

function showMessage(text) {
    message.textContent = text;
}

async function loadCourses() {
    try {
        const result = await apiRequest("GET");
        courses = result.data || [];
        displayCourses(courses);
    } catch (error) {
        showMessage(error.message);
        courseContainer.replaceChildren();
    }
}

function displayCourses(list) {
    courseContainer.replaceChildren();

    if (list.length === 0) {
        courseContainer.textContent = "No courses found.";
        return;
    }

    list.forEach(course => {
        const card = document.createElement("div");
        card.className = "course-card";

        const icon = document.createElement("i");
        icon.className = course.icon || "fas fa-book";
        icon.setAttribute("aria-hidden", "true");

        const title = document.createElement("h3");
        title.textContent = course.name;

        const description = document.createElement("p");
        description.textContent = course.description;

        card.append(icon, title, description);

        if (window.courseAdmin) {
            const actions = document.createElement("div");
            actions.className = "course-actions";

            const editButton = document.createElement("button");
            editButton.type = "button";
            editButton.textContent = "Edit";
            editButton.addEventListener("click", () => editCourse(course));

            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.className = "delete-btn";
            deleteButton.textContent = "Delete";
            deleteButton.addEventListener("click", () => deleteCourse(course.id));

            actions.append(editButton, deleteButton);
            card.appendChild(actions);
        }

        courseContainer.appendChild(card);
    });
}

function editCourse(course) {
    courseId.value = course.id;
    courseName.value = course.name;
    courseDescription.value = course.description;
    courseIcon.value = course.icon || "fas fa-book";
    formHeading.textContent = "Update Course";
    cancelEdit.hidden = false;
    courseName.focus();
}

function resetForm() {
    if (!form) return;

    form.reset();
    courseId.value = "";
    courseIcon.value = "fas fa-book";
    formHeading.textContent = "Add New Course";
    cancelEdit.hidden = true;
}

if (form) {
    form.addEventListener("submit", async event => {
        event.preventDefault();

        const id = courseId.value;
        const data = {
            name: courseName.value.trim(),
            description: courseDescription.value.trim(),
            icon: courseIcon.value.trim() || "fas fa-book"
        };

        if (!data.name || !data.description) {
            showMessage("Enter course name and description.");
            return;
        }

        try {
            const result = await apiRequest(id ? "PUT" : "POST",
                id ? { ...data, id: Number(id) } : data);

            showMessage(result.message);
            resetForm();
            await loadCourses();
        } catch (error) {
            showMessage(error.message);
        }
    });

    cancelEdit.addEventListener("click", resetForm);
}

async function deleteCourse(id) {
    if (!confirm("Are you sure you want to delete this course?")) return;

    try {
        const result = await apiRequest("DELETE", { id: Number(id) });
        showMessage(result.message);
        await loadCourses();
    } catch (error) {
        showMessage(error.message);
    }
}

searchInput.addEventListener("input", () => {
    const text = searchInput.value.toLowerCase().trim();

    displayCourses(courses.filter(course =>
        course.name.toLowerCase().includes(text) ||
        course.description.toLowerCase().includes(text)
    ));
});

loadCourses();