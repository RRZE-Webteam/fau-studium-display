/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/block/block.json":
/*!******************************!*\
  !*** ./src/block/block.json ***!
  \******************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"fau-studium/display","version":"1.0.4","title":"FAU-Studium Display","category":"rrze","description":"","example":{},"attributes":{"degreeProgram":{"type":"number","default":0},"selectedFaculties":{"type":"array","default":[]},"selectedDegrees":{"type":"array","default":[]},"selectedSpecialWays":{"type":"array","default":[]},"language":{"type":"string","default":""},"format":{"type":"string","default":"full"},"showSearch":{"type":"boolean","default":false},"showTitle":{"type":"boolean","default":true},"selectedItemsGrid":{"type":"array","default":["teaser_image","title","subtitle","degree","start","admission_requirements","area_of_study"]},"selectedItemsTable":{"type":"array","default":["teaser_image","title","degree","start","location","admission_requirements","german_language_skills_for_international_students","application_deadline"]},"selectedItemsFull":{"type":"array","default":["teaser_image","title","subtitle","entry_text","fact_sheet","content.about","content.structure","content.specializations","content.qualities_and_skills","content.why_should_study","content.career_prospects","content.special_features","combinations","videos","info_internationals_link","admission_requirements_application","apply_now_link","student_advice","subject_specific_advice","links.organizational","links.downloads","links.additional_information","benefits"]},"selectedSearchFilters":{"type":"array","default":["admission_requirements","attribute","degree","german_language_skills_for_international_students","faculty","semester","study_location","subject_group","teaching_language"]}},"supports":{"html":false},"textdomain":"fau-studium-display","editorScript":"file:./index.js","editorStyle":"file:./index.css","viewScript":"file:./view.js","render":"file:./render.php"}');

/***/ }),

/***/ "./src/block/edit.tsx":
/*!****************************!*\
  !*** ./src/block/edit.tsx ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/block/editor.scss");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







const Edit = ({
  attributes,
  setAttributes
}) => {
  var _degreeProgram$toStri, _degreeProgram$toStri2;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)();
  const {
    degreeProgram,
    language,
    format = 'full',
    showSearch,
    showTitle = true
  } = attributes;
  const [degreePrograms, setDegreePrograms] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$degre;
    return (_fauStudiumData$degre = fauStudiumData?.degreePrograms) !== null && _fauStudiumData$degre !== void 0 ? _fauStudiumData$degre : [];
  });
  const [itemsGrid, setItemsGrid] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$items;
    return (_fauStudiumData$items = fauStudiumData?.itemsGridOptions) !== null && _fauStudiumData$items !== void 0 ? _fauStudiumData$items : [];
  });
  const [itemsFull, setItemsFull] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$items2;
    return (_fauStudiumData$items2 = fauStudiumData?.itemsFullOptions) !== null && _fauStudiumData$items2 !== void 0 ? _fauStudiumData$items2 : [];
  });
  const [searchFilters, setSearchFilters] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$searc;
    return (_fauStudiumData$searc = fauStudiumData?.searchFilters) !== null && _fauStudiumData$searc !== void 0 ? _fauStudiumData$searc : [];
  });
  const [faculties] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$facul;
    return (_fauStudiumData$facul = fauStudiumData?.facultiesOptions) !== null && _fauStudiumData$facul !== void 0 ? _fauStudiumData$facul : [];
  });
  const [degrees] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$degre2;
    return (_fauStudiumData$degre2 = fauStudiumData?.degreesOptions) !== null && _fauStudiumData$degre2 !== void 0 ? _fauStudiumData$degre2 : [];
  });
  const [specialWays] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(() => {
    var _fauStudiumData$speci;
    return (_fauStudiumData$speci = fauStudiumData?.specialWaysOptions) !== null && _fauStudiumData$speci !== void 0 ? _fauStudiumData$speci : [];
  });
  const {
    selectedItemsGrid = ["teaser_image", "title", "subtitle", "degree", "start", "admission_requirements", "area_of_study"]
  } = attributes;
  const {
    selectedItemsFull = ["teaser_image", "title", "subtitle", "entry_text", "fact_sheet", "content.about", "content.structure", "content.specializations", "content.qualities_and_skills", "content.why_should_study", "content.career_prospects", "content.special_features", "combinations", "videos", "info_internationals_link", "admission_requirements_application", "apply_now_link", "student_advice", "subject_specific_advice", "links.organizational", "links.downloads", "links.additional_information", "benefits"]
  } = attributes;
  const {
    selectedSearchFilters = ["admission-requirement", "attribute", "degree", "german-language-skills-for-international-students", "faculty", "semester", "study-location", "subject-group", "teaching-language"]
  } = attributes;
  const {
    selectedFaculties = [],
    selectedDegrees = [],
    selectedSpecialWays = []
  } = attributes;
  const [selectedFormat, setSelectedFormat] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(format);
  const onChangeFormat = value => {
    if (typeof value === 'string') {
      setSelectedFormat(value);
      setAttributes({
        format: value
      });
    }
  };
  const onChangeLanguage = value => {
    if (typeof value === 'string') {
      setAttributes({
        language: value
      });
    }
  };
  const onChangeDegreeProgram = value => {
    if (typeof value === 'string') {
      const numericValue = parseInt(value, 10);
      if (!isNaN(numericValue)) {
        setAttributes({
          degreeProgram: numericValue
        });
      }
    }
  };
  const onChangeShowSearch = value => {
    setAttributes({
      showSearch: value
    });
  };
  const onChangeShowTitle = value => {
    setAttributes({
      showTitle: value
    });
  };
  const toggleItemGrid = value => {
    const updated = selectedItemsGrid.includes(value) ? selectedItemsGrid.filter(v => v !== value) : [...selectedItemsGrid, value];
    setAttributes({
      selectedItemsGrid: [...updated]
    });
  };
  const toggleItemFull = value => {
    const updated = selectedItemsFull.includes(value) ? selectedItemsFull.filter(v => v !== value) : [...selectedItemsFull, value];
    setAttributes({
      selectedItemsFull: [...updated]
    });
  };
  const toggleSearchItem = value => {
    const updated = selectedSearchFilters.includes(value) ? selectedSearchFilters.filter(v => v !== value) : [...selectedSearchFilters, value];
    setAttributes({
      selectedSearchFilters: [...updated]
    });
  };
  const toggleFaculties = value => {
    const updated = selectedFaculties.includes(value) ? selectedFaculties.filter(v => v !== value) : [...selectedFaculties, value];
    setAttributes({
      selectedFaculties: [...updated]
    });
  };
  const toggleDegrees = value => {
    const updated = selectedDegrees.includes(value) ? selectedDegrees.filter(v => v !== value) : [...selectedDegrees, value];
    setAttributes({
      selectedDegrees: [...updated]
    });
  };
  const toggleSpecialWays = value => {
    const updated = selectedSpecialWays.includes(value) ? selectedSpecialWays.filter(v => v !== value) : [...selectedSpecialWays, value];
    setAttributes({
      selectedSpecialWays: [...updated]
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    ...blockProps,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('General Settings', 'fau-studium-display'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ComboboxControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Format', 'fau-studium-display'),
          value: selectedFormat.toString(),
          options: [{
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Full', 'fau-studium-display'),
            value: 'full'
          },
          // Kompletter Studiengang
          {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Infobox', 'fau-studium-display'),
            value: 'box'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Grid', 'fau-studium-display'),
            value: 'grid'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Table', 'fau-studium-display'),
            value: 'table'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('List', 'fau-studium-display'),
            value: 'list'
          } // Früher 'short'
          ],
          onChange: onChangeFormat
        }), (selectedFormat === "grid" || selectedFormat === "table" || selectedFormat === "list") && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Search', 'fau-studium-display'),
          checked: !!showSearch,
          onChange: onChangeShowSearch
        }), showSearch && (selectedFormat === "grid" || selectedFormat === "table" || selectedFormat === "list") && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("h3", {
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Search Filters', 'fau-studium-display')
          }), searchFilters.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
            label: item.label,
            checked: selectedSearchFilters.includes(item.value),
            onChange: () => toggleSearchItem(item.value)
          }, item.value))]
        }), (selectedFormat === "full" || selectedFormat === "box") && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ComboboxControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Degree Program', 'fau-studium-display'),
            value: (_degreeProgram$toStri = degreeProgram?.toString?.()) !== null && _degreeProgram$toStri !== void 0 ? _degreeProgram$toStri : '',
            options: degreePrograms !== null && degreePrograms !== void 0 ? degreePrograms : [],
            onChange: onChangeDegreeProgram
          })
        }), selectedFormat === "box" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Title', 'fau-studium-display'),
          value: (_degreeProgram$toStri2 = degreeProgram?.toString?.()) !== null && _degreeProgram$toStri2 !== void 0 ? _degreeProgram$toStri2 : '',
          checked: !!showTitle,
          onChange: onChangeShowTitle
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ComboboxControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Language', 'fau-studium-display'),
          value: language,
          options: [{
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'fau-studium-display'),
            value: ''
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('German', 'fau-studium-display'),
            value: 'de'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('English', 'fau-studium-display'),
            value: 'en'
          }],
          onChange: onChangeLanguage
        })]
      }), selectedFormat === "grid" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select items', 'fau-studium-display'),
        initialOpen: true,
        children: itemsGrid.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: item.label,
          checked: selectedItemsGrid.includes(item.value),
          onChange: () => toggleItemGrid(item.value)
        }, item.value))
      }), selectedFormat === "full" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select items', 'fau-studium-display'),
        initialOpen: true,
        children: itemsFull.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: item.label,
          checked: selectedItemsFull.includes(item.value),
          onChange: () => toggleItemFull(item.value)
        }, item.value))
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Filter Programs', 'fau-studium-display'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("h3", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Faculties', 'fau-studium-display')
        }), faculties.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: item.label,
          checked: selectedFaculties.includes(item.value),
          onChange: () => toggleFaculties(item.value)
        }, item.value)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("hr", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("h3", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Degrees', 'fau-studium-display')
        }), degrees.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: item.label,
          checked: selectedDegrees.includes(item.value),
          onChange: () => toggleDegrees(item.value)
        }, item.value)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("hr", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("h3", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Special ways to study', 'fau-studium-display')
        }), specialWays.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: item.label,
          checked: selectedSpecialWays.includes(item.value),
          onChange: () => toggleSpecialWays(item.value)
        }, item.value))]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default()), {
      block: "fau-studium/display",
      attributes: attributes
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Edit);

/***/ }),

/***/ "./src/block/editor.scss":
/*!*******************************!*\
  !*** ./src/block/editor.scss ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/block/save.tsx":
/*!****************************!*\
  !*** ./src/block/save.tsx ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ save)
/* harmony export */ });
//It's a dynamic block
function save() {
  return null;
}

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************!*\
  !*** ./src/block/index.tsx ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./src/block/edit.tsx");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./save */ "./src/block/save.tsx");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./src/block/block.json");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './style.scss';

/**
 * Internal dependencies
 */




/*
 * Icon
 */

const icon = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 448 512",
  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("path", {
    d: "M219.3 .5c3.1-.6 6.3-.6 9.4 0l200 40C439.9 42.7 448 52.6 448 64s-8.1 21.3-19.3 23.5L352 102.9l0 57.1c0 70.7-57.3 128-128 128s-128-57.3-128-128l0-57.1L48 93.3l0 65.1 15.7 78.4c.9 4.7-.3 9.6-3.3 13.3s-7.6 5.9-12.4 5.9l-32 0c-4.8 0-9.3-2.1-12.4-5.9s-4.3-8.6-3.3-13.3L16 158.4l0-71.8C6.5 83.3 0 74.3 0 64C0 52.6 8.1 42.7 19.3 40.5l200-40zM111.9 327.7c10.5-3.4 21.8 .4 29.4 8.5l71 75.5c6.3 6.7 17 6.7 23.3 0l71-75.5c7.6-8.1 18.9-11.9 29.4-8.5C401 348.6 448 409.4 448 481.3c0 17-13.8 30.7-30.7 30.7L30.7 512C13.8 512 0 498.2 0 481.3c0-71.9 47-132.7 111.9-153.6z"
  })
});

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__.name, {
  icon: icon,
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_2__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map