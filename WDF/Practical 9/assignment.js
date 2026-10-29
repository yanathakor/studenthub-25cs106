const assignmentBox = document.getElementById("assignmentBox");
const searchInput = document.getElementById("searchAssignment");

let assignments = [];

fetch("assignment.json")
    .then(response => {
        if (!response.ok) {
            throw new Error("Unable to load JSON file");
        }

        return response.json();
    })
    .then(data => {
        assignments = data.taskList;
        displayAssignments(assignments);
    })
    .catch(error => {
        assignmentBox.innerHTML =
            "<p>Unable to load assignments.</p>";

        console.error(error);
    });


function displayAssignments(data) {

    assignmentBox.innerHTML = "";

    if (data.length === 0) {
        assignmentBox.innerHTML =
            "<p>No assignments found.</p>";
        return;
    }

    data.forEach(task => {

        const div = document.createElement("div");

        div.className = "assignment";

        div.innerHTML = `
            <h3>${task.courseName}</h3>

            <p>${task.taskDetails}</p>

            <span>Submission Date : ${task.submissionDate}</span>
        `;

        assignmentBox.appendChild(div);
    });
}


searchInput.addEventListener("input", function () {

    const searchText =
        searchInput.value.toLowerCase();

    const filteredAssignments =
        assignments.filter(task =>
            task.courseName
                .toLowerCase()
                .includes(searchText) ||

            task.taskDetails
                .toLowerCase()
                .includes(searchText)
        );

    displayAssignments(filteredAssignments);
});