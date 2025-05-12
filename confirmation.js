document.addEventListener('DOMContentLoaded', function() {
    // Retrieve the appointment details from localStorage
    const appointmentDetails = JSON.parse(localStorage.getItem("appointmentDetails"));

    if (appointmentDetails) {
        // Display the appointment details on the confirmation page
        document.getElementById("confirmed-service").innerText = appointmentDetails.service;
        document.getElementById("confirmed-staff").innerText = appointmentDetails.staff;
        document.getElementById("confirmed-time").innerText = appointmentDetails.datetime;
        document.getElementById("confirmed-duration").innerText = appointmentDetails.duration;

        // Optional: Clear the localStorage after displaying the confirmation details
        localStorage.removeItem("appointmentDetails");
    } else {
        // If no appointment data is found, redirect to home
        alert("No appointment details found.");
        window.location.href = "index.html";
    }
});