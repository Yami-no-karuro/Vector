"use strict";
(self["webpackChunkvector"] = self["webpackChunkvector"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _styles_app_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./styles/app.scss */ "./assets/styles/app.scss");

(function () {
  'use-strict';

  document.addEventListener('DOMContentLoaded', function () {
    var logsTable = document.getElementById("logs-table-wrapper");
    if (null !== logsTable) {
      new gridjs.Grid({
        columns: ['ID', 'Domain', 'Content'],
        sort: true,
        server: {
          url: '/api/v1/logs',
          then: function then(data) {
            return data.data.entries.map(function (el) {
              return [el.ID, el.domain, el.log];
            });
          },
          total: function total(data) {
            return data.data.total;
          }
        },
        pagination: {
          limit: 25,
          server: {
            url: function url(prev, page, limit) {
              return "".concat(prev, "?limit=").concat(limit, "&offset=").concat(page * limit);
            }
          }
        },
        search: {
          server: {
            url: function url(prev, keyword) {
              return "".concat(prev, "?search=").concat(keyword);
            }
          }
        },
        language: {
          'search': {
            'placeholder': 'Find in entries'
          },
          'pagination': {
            'previous': 'Previus',
            'next': 'Next',
            'showing': 'Displaying',
            'results': function results() {
              return 'Records';
            }
          }
        }
      }).render(logsTable);
    }
  });
})();

/***/ }),

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/app.js"));
/******/ }
]);