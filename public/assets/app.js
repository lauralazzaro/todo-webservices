/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import $ from "jquery";
import "./styles/app.css";
import "bootstrap-datepicker";
import "bootstrap-datepicker/dist/css/bootstrap-datepicker.css";

$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd/mm/yyyy",
    autoclose: true,
    todayHighlight: true,
    todayBtn: true,
  });
});

window.changeTaskStatus = function(selectElement, taskId) {
  const selectedStatus = selectElement.value;
  window.location.href = `/tasks/${taskId}/change-status/${selectedStatus}`;
};