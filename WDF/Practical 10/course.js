const courseContainer = document.getElementById("courseContainer");
const searchInput = document.querySelector(".search input");

let courses = [];

fetch("course.json")
    .then(response => {
        if (!response.ok) {
            throw new Error("Unable to load course data");
        }

        return response.json();
    })
    .then(data => {
        courses = data.courses;
        displayCourses(courses);
    })
    .catch(error => {
        courseContainer.innerHTML =
            "<p>Unable to load courses.</p>";

        console.error(error);
    });


function displayCourses(data) {

    courseContainer.innerHTML = "";

    if (data.length === 0) {
        courseContainer.innerHTML =
            "<p>No courses found.</p>";
        return;
    }

    data.forEach(course => {

        const div = document.createElement("div");

        div.className = "course-card";

        div.innerHTML = `
            <i class="${course.icon}"></i>

            <h3>${course.name}</h3>

            <p>${course.description}</p>

            <a href="#">View</a>
        `;

        courseContainer.appendChild(div);
    });
}


searchInput.addEventListener("input", function () {

    const searchText =
        searchInput.value.toLowerCase();

    const filteredCourses =
        courses.filter(course =>
            course.name
                .toLowerCase()
                .includes(searchText) ||

            course.description
                .toLowerCase()
                .includes(searchText)
        );

    displayCourses(filteredCourses);
});