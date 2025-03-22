function sortAppointmentsByStatus() {
    let table = document.querySelector(".appointments table tbody");
    let rows = Array.from(table.getElementsByTagName("tr"));
    let selectedStatus = document.getElementById("sortStatus").value;

    if (selectedStatus === "all") {
        rows.forEach(row => row.style.display = "table-row");
        return;
    }

    rows.forEach(row => {
        let statusCell = row.querySelector(".status");
        let statusText = statusCell.textContent.trim().toLowerCase();

        if (statusText.includes(selectedStatus)) {
            row.style.display = "table-row";
        } else {
            row.style.display = "none";
        }
    });
}
