"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./public/assets/app.js":
/*!******************************!*\
  !*** ./public/assets/app.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _styles_app_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styles/app.css */ "./public/assets/styles/app.css");
/* harmony import */ var bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! bootstrap-datepicker */ "./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js");
/* harmony import */ var bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var bootstrap_datepicker_dist_css_bootstrap_datepicker_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! bootstrap-datepicker/dist/css/bootstrap-datepicker.css */ "./node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css");

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)




jquery__WEBPACK_IMPORTED_MODULE_1___default()(document).ready(function () {
  jquery__WEBPACK_IMPORTED_MODULE_1___default()(".datepicker").datepicker({
    format: "dd/mm/yyyy",
    autoclose: true,
    todayHighlight: true,
    todayBtn: true
  });
});
window.changeTaskStatus = function (selectElement, taskId) {
  var selectedStatus = selectElement.value;
  var confirmChange = confirm("Are you sure you want to change the task status to *".concat(selectedStatus, "*?"));
  if (confirmChange) {
    window.location.href = "/tasks/".concat(taskId, "/change-status/").concat(selectedStatus);
  }
};

/***/ }),

/***/ "./public/assets/styles/app.css":
/*!**************************************!*\
  !*** ./public/assets/styles/app.css ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_bootstrap-datepicker_dist_css_bootstrap-datepicker_css","vendors-node_modules_bootstrap-datepicker_dist_js_bootstrap-datepicker_js-node_modules_bootst-d86411"], () => (__webpack_exec__("./node_modules/bootstrap/dist/js/bootstrap.bundle.js"), __webpack_exec__("./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"), __webpack_exec__("./public/assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);