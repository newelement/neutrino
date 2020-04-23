let $ = require('jquery');
let Vue2 = require('vue');
let slugger = require('slugger');
import Sortable from 'sortablejs';
import select2 from 'select2';
import Swal from 'sweetalert2';
import PaceProgressBar from 'pace-progressbar';
import datepicker from 'js-datepicker'
import flatpickr from "flatpickr";
import moment from 'moment';
window.moment = moment;
import Choices from 'choices.js'
import Tabulator from 'tabulator-tables';
import 'pace-progressbar/themes/blue/pace-theme-minimal.css';
import axios from 'axios';

import tinymce from 'tinymce';
import 'tinymce/themes/silver';
import 'tinymce/themes/mobile';
import 'tinymce/plugins/autolink/plugin.js';
import 'tinymce/plugins/advlist/plugin.js';
import 'tinymce/plugins/lists/plugin.js';
import 'tinymce/plugins/link/plugin.js';
import 'tinymce/plugins/image/plugin.js';
import 'tinymce/plugins/imagetools/plugin.js';
import 'tinymce/plugins/charmap/plugin.js';
import 'tinymce/plugins/print/plugin.js';
import 'tinymce/plugins/preview/plugin.js';
import 'tinymce/plugins/anchor/plugin.js';
import 'tinymce/plugins/hr/plugin.js';
import 'tinymce/plugins/pagebreak/plugin.js';
import 'tinymce/plugins/searchreplace/plugin.js';
import 'tinymce/plugins/visualblocks/plugin.js';
import 'tinymce/plugins/visualchars/plugin.js';
import 'tinymce/plugins/code/plugin.js';
import 'tinymce/plugins/insertdatetime/plugin.js';
import 'tinymce/plugins/fullscreen/plugin.js';
import 'tinymce/plugins/media/plugin.js';
import 'tinymce/plugins/wordcount/plugin.js';
import 'tinymce/plugins/save/plugin.js';
import 'tinymce/plugins/nonbreaking/plugin.js';
import 'tinymce/plugins/table/plugin.js';
import 'tinymce/plugins/emoticons/plugin.js';
import 'tinymce/plugins/template/plugin.js';
import 'tinymce/plugins/paste/plugin.js';
import 'tinymce/plugins/textpattern/plugin.js';
import 'tinymce/plugins/directionality/plugin.js';

PaceProgressBar.start();

const HTTP = axios.create(axios.defaults.headers.common = {
	'X-Requested-With': 'XMLHttpRequest',
	'X-CSRF-TOKEN' : app.csrfToken,
	'Content-Type': 'multipart/form-data'
});

let chosenFeatured = {};
let chosenAttributes = [];
let $form = $('.media-drop-zone');
let droppedFiles = false;
let cf_config;
let $clearDatetimePicker = document.querySelectorAll('.clear-datetime-picker');

let refundObj = {
    amount: 0.00,
    line_id: 0,
    ref_id: '',
    qty: 0,
    line_item: '',
    full_amount: 1
};

let fields = [
	{ label: 'Text', type: 'text'},
	{ label: 'Number', type: 'number'},
	{ label: 'Decimal', type: 'decimal'},
	{ label: 'Checkbox', type: 'checkbox'},
	{ label: 'Radio', type: 'radio'},
	{ label: 'Multi-line Text', type: 'textarea'},
	{ label: 'Email', type: 'email'},
	{ label: 'Date', type: 'date'},
	{ label: 'Dropdown', type: 'select'},
	{ label: 'File', type: 'file'},
	{ label: 'Image', type: 'image'},
	{ label: 'Rich Text Editor', type: 'editor'}
];

let editor = document.querySelector('.editor');
let smallEditor = document.querySelector('.small-editor');
let cfeditor = document.querySelector('.cf-editor');

if( smallEditor ){
    var editor_config_small = {
        path_absolute : "/",
        "selector": ".small-editor",
        height: 230,
        width: '100%',
        skin: "oxide-dark",
        plugins: [
          "advlist autolink lists link hr anchor pagebreak",
          "wordcount visualblocks visualchars code",
          "insertdatetime nonbreaking directionality",
          "paste textpattern"
        ],
        menubar:false,
        statusbar: false,
        toolbar: "undo redo | formatselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link",
        fontsize_formats: "8px 10px 12px 14pt 16px 18px 20px 22px 24px 26px 28px 30px 36px",
        relative_urls: false,
        mobile: {
            theme: 'mobile',
            plugins: [ 'lists', 'autolink', 'link' ],
            toolbar: [ 'undo', 'bold', 'billist', 'link', 'italic', 'styleselect' ]
        },
        content_css : editorCss,
    };

    tinymce.init(
        editor_config_small
    );
}

if( editor ){
	var editor_config = {
	    path_absolute : "/",
	    "selector": ".editor",
		height: 600,
		width: '100%',
        skin: "oxide-dark",
	    plugins: [
	      "advlist autolink lists link image imagetools charmap print preview hr anchor pagebreak",
	      "searchreplace wordcount visualblocks visualchars code fullscreen",
	      "insertdatetime media nonbreaking table directionality",
	      "template paste textpattern"
	    ],
        //menubar:false,
        menubar: 'insert view format tools',
	    toolbar: "insertfile undo redo | styleselect fontsizeselect | pastetext | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | link image | bullist numlist | table | outdent indent | media",
		fontsize_formats: "8px 10px 12px 14pt 16px 18px 20px 22px 24px 26px 28px 30px 36px",
        relative_urls: false,
		mobile: {
		    theme: 'mobile',
		    plugins: [ 'lists', 'autolink', 'image', 'link' ],
		    toolbar: [ 'undo', 'bold', 'billist', 'link', 'italic', 'styleselect', 'image' ]
		},
		file_picker_callback: function(callback, value, meta) {
		    //console.log(meta, value);
			var type = 'image' === meta.filetype ? 'image' : 'file';
			showFMeditor(type);
			window.addEventListener('message', (event) => {
				if( event.data.mceAction === 'insert' ){
					let name = event.data.content.split('/').pop();
					let obj = type === 'image'? { alt: '' } : { text: name } ;
					callback(event.data.content, obj);
				}
			});
		},
        body_id: 'block-editor',
        style_formats: editorStyles,
        content_css : editorCss,
        style_formats_merge: true,
        content_css_cors: true
	};

	tinymce.init(
		editor_config
	);
}

if( typeof editorCss !== 'undefined' && cfeditor ){

    cf_config = {
        path_absolute : "/",
        "selector": ".cf-editor",
        height: 600,
        width: '100%',
        skin: "oxide-dark",
        plugins: [
          "advlist autolink lists link image imagetools charmap print preview hr anchor pagebreak",
          "searchreplace wordcount visualblocks visualchars code fullscreen",
          "insertdatetime media nonbreaking table directionality",
          "template paste textpattern"
        ],
        //menubar:false,
        menubar: 'insert view format tools',
	    toolbar: "insertfile undo redo | styleselect fontsizeselect | pastetext | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | link image | bullist numlist | table | outdent indent | media",
        fontsize_formats: "8px 10px 12px 14pt 16px 18px 20px 22px 24px 26px 28px 30px 36px",
        relative_urls: false,
        mobile: {
            theme: 'mobile',
            plugins: [ 'lists', 'autolink', 'image', 'link' ],
            toolbar: [ 'undo', 'bold', 'billist', 'link', 'italic', 'styleselect', 'image' ]
        },
        file_picker_callback: function(callback, value, meta) {
            //console.log(meta, value);
            var type = 'image' === meta.filetype ? 'image' : 'file';
            showFMeditor(type);
            window.addEventListener('message', (event) => {
                if( event.data.mceAction === 'insert' ){
                    let name = event.data.content.split('/').pop();
                    let obj = type === 'image'? { alt: '' } : { text: name } ;
                    callback(event.data.content, obj);
                }
            });
        },
        body_id: 'block-editor',
        style_formats: editorStyles,
        style_formats_merge: true,
        content_css : editorCss,
        content_css_cors: true
    };

    tinymce.init(cf_config);
}


function readURL(input) {
	$('#preview').hide();
  	if (input.files && input.files[0]) {
    	var reader = new FileReader();

	    reader.onload = function(e) {
	      $('#preview').attr('src', e.target.result);
		  $('#preview').show();
		  uploadProductFile();
	    }

    	reader.readAsDataURL(input.files[0]);
  	}
}

var ID = function () {
  return '_' + Math.random().toString(36).substr(2, 9);
};

function addFieldType(type, repeater){

	let id = ID();

	let html = '<li id="'+id+'" data-row-id="'+id+'">';
	html += '<div class="field-type-row" data-row-id="'+id+'">';

	switch(type){
		case 'text':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Text</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="text"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'email':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Email</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="email"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'number':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Number</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="number"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'decimal':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Decimal</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="number"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'textarea':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Multi-line Text</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="textarea"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'date':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Date</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="date"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'file':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">File Upload</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="file"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Multiple Files</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_multiple['+id+']"value="1"> Yes</label> <label><input type="radio" name="field_multiple['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Allowed Files Types</div>';
					html += '<div class="field-col"><label><input type="text" name="field_filetypes['+id+']" value="*"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'image':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Image Upload</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="image"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Multiple Files</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_multiple['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_multiple['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Allowed Image Types</div>';
					html += '<div class="field-col"><label><input type="text" name="field_filetypes['+id+']" value="*"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'checkbox':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Checkboxes</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="checkbox"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Checkboxes</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each checkbox setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

			html += '</div>';

		break;

		case 'radio':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Radios</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="radio"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Radios</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each radio setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

			html += '</div>';

		break;

		case 'select':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Dropdown</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="select"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Options</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each option setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Empty First Option?</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_firstoption['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_firstoption['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Select Multiple</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_select_multiple['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_select_multiple['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'editor':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Rich Text Editor</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="editor"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

		break;

		case 'repeater':
		html += '<div class="field-row">';
			html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Repeater</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-repeater="'+repeater+'" data-row-id="'+id+'" href="/">&times;</a></div>';
		html += '</div>';

		html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
			html += '<div class="field-row">';
				html += '<div class="label-col">Field Label</div>';
				html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="repeater"></div>';
			html += '</div>';

			html += '<div class="field-row">';
				html += '<div class="label-col">Field Name</div>';
				html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
			html += '</div>';

			html += '<div class="field-row">';
				html += '<div class="label-col full">Repeater Fields <div class="choose-repeater-fields">'+fieldsDropDown(id)+'</div></div>';
				html += '<div class="field-col full has-repeater">';
					html += '<ul class="repeater-fields-list" id="repeater-fields'+id+'">';
					html += '</ul>';
				html += '</div>';
			html += '</div>';
		break;
	}

	if( repeater ){
		html += '<input type="hidden" name="field_repeater['+id+']" value="'+repeater+'" >';
	}

	html += '</div>';
	html += '</li>';

	if( repeater ){
		$('#repeater-fields'+repeater).append(html);
		initRepeaterSorter(repeater);
	} else {
		$('.fields-list').append(html);
	}

}

function initRepeaterSorter(repeater){
	let repeaterList = document.querySelector('.repeater-fields-list');
	Sortable.create(repeaterList, {
		handle: '.field-sort',
		easing: "cubic-bezier(1, 0, 0, 1)",
		animation: 150,
		onEnd: function (e) {
			updateGroupFieldsSort();
		},
		onAdd: function (e) {
			//console.log('ADD',e);
		},
		onStart: function (evt) {
			evt.oldIndex;
		},
	});
}

function addFormFieldType(type){

	let id = ID();

	let html = '<li id="'+id+'" data-row-id="'+id+'">';
	html += '<div class="field-type-row" data-row-id="'+id+'">';

	switch(type){
		case 'text':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Text</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row"  data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="text"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
				/*
				html += '<div class="field-row">';
					html += '<div class="label-col">Conditional</div>';
					html += '<div class="field-col"><label><input type="radio" data-conditional-toggle="'+id+'" name="field_conditional['+id+']" value="1"> Yes</label></div>';
				html += '</div>';

				html += '<div id="conditional-toggle" class="field-row field-conditonal-rules">';
					html += '<div class="label-col">Conditional Rules</div>';
					html += '<div class="field-col">';
					    html += '<div class="select-wrapper"><select name="field_conditional_view['+id+']"><option value="show">Show</option><option value="hide">Hide</option></select></div>';
                        html += '<span class="notes">You must add the field you want to use for the conditional value and save the form first.</span>';
					html += '</div>';
				html += '</div>';*/

			html += '</div>';

		break;

		case 'email':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Email</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="email"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'number':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Number</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="number"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'decimal':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Decimal</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="number"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'textarea':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Multi-line Text</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="textarea"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'date':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Date</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="date"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'file':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">File Upload</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="file"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Multiple Files</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_multiple['+id+']"value="1"> Yes</label> <label><input type="radio" name="field_multiple['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Allowed Files Types</div>';
					html += '<div class="field-col"><label><input type="text" name="field_filetypes['+id+']" value="*"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'image':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Image Upload</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="image"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Multiple Files</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_multiple['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_multiple['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Allowed Image Types</div>';
					html += '<div class="field-col"><label><input type="text" name="field_filetypes['+id+']" value="*"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'checkbox':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Checkboxes</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="checkbox"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Checkboxes</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each checkbox setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'radio':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Radios</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="radio"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Radios</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each radio setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;

		case 'select':

			html += '<div class="field-row">';
				html += '<div class="field-sort"><i class="far fa-sort"></i></div><div class="field-type-title">Dropdown</div><div class="field-row-options"><a class="collapse-field-row" href="/" data-row-id="'+id+'"><i class="fas fa-angle-down"></i></a> <a class="remove-field-row" data-row-id="'+id+'" href="/">&times;</a></div>';
			html += '</div>';

			html += '<div id="field-group-'+id+'" class="field-group" data-row-id="'+id+'">';
				html += '<div class="field-row">';
					html += '<div class="label-col">Field Label</div>';
					html += '<div class="field-col"><input id="label-'+id+'" class="field-label" type="text" name="field_label['+id+']" required><input type="hidden" name="field_type['+id+']" value="select"></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Field Name</div>';
					html += '<div class="field-col"><input id="name-'+id+'" type="text" name="field_name['+id+']" required></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Options</div>';
					html += '<div class="field-col"><textarea name="field_options['+id+']" placeholder="Label:value"></textarea><span class="notes">Enter each option setup on a new line. Example:<br>Label:value<br>Label:value</span></div>';
				html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Empty First Option?</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_firstoption['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_firstoption['+id+']" checked value="0"> No</label></div>';
				html += '</div>';

				html += '<div class="field-row">';
				html += '<div class="label-col">Select Multiple</div>';
				html += '<div class="field-col">';
					html += '<label><input type="radio" name="field_select_multiple['+id+']" value="1"> Yes</label>';
					html += '<label><input type="radio" name="field_select_multiple['+id+']" checked value="0"> No</label>';
				html += '</div>';
			html += '</div>';

				html += '<div class="field-row">';
					html += '<div class="label-col">Required</div>';
					html += '<div class="field-col"><label><input type="radio" name="field_required['+id+']" value="1"> Yes</label> <label><input type="radio" name="field_required['+id+']" checked value="0"> No</label></div>';
				html += '</div>';
			html += '</div>';

		break;
	}

	html += '</div>';
	html += '</li>';

	$('.form-fields-list').append(html);
}

function fieldsDropDown(id){
	let html = '<div class="fields-drop-down">';
	html += '<span>Add Field <i class="fas fa-plus"></i></span>';
	html += '<ul>';
	fields.forEach(function(v){
		html += '<li data-type="'+v.type+'" data-id="'+id+'">'+v.label+'</li>';
	});
	html += '</ul>';
	html += '</div>';
	return html;
}

function updateGroupSort(){

	let form = document.getElementById('group-fields');
	let formData = new FormData(form);
	let id = $('#group-id').val();

	HTTP.post('/admin/custom-fields/group/'+id+'/fields/sort', formData)
	.then(response => {

	})
	.catch(e => {
		//console.log('sort error');
	});
}

function updateFieldSort(){

	let form = document.getElementById('form-fields');
	let formData = new FormData(form);
	let id = $('#form-id').val();

	HTTP.post('/admin/form-fields/'+id+'/sort', formData)
	.then(response => {

	})
	.catch(e => {
		//console.log('sort error');
	});
}

let menuItems = [];

$.fn.sortableListsToJson = function (){
	var arr = [];
    $(this).children('li').each(function () {
    	var li = $(this);
        var object = li.data();
        arr.push(object);
        var ch = li.children('ul,ol').sortableListsToJson();
        if (ch.length > 0) {
            object.children = ch;
        } else {
            delete object.children;
        }
    });
	//console.log(arr);
    return arr;
};

function updateMenuItems(){
	let jsons = $('.menu-items-drop').sortableListsToJson();
	let menu_id = $('#menu-id').val();
	let items = [];
	jsons.forEach(function(v){
		items.push($.param(v));
	});

	let obj = { menu_id : menu_id, items: jsons };

	HTTP.post('/admin/menu-items', obj)
	.then(response => {
		//console.log(response.data);
	})
	.catch(e => {
		console.log('check error');
	});
}

function updateObjectRepeaterSort(){

	let form = document.getElementById('object-form');
	if( form ){
		let formData = new FormData(form);
		let action = form.getAttribute('action');
		HTTP.post(action, formData)
		.then(response => {
		})
		.catch(e => {
			console.log('sort error');
		});
	}
}

function updateTaxSort(type){
    let items = document.querySelectorAll('.tax-item');
    let formData = new FormData;
    items.forEach((v) => {
        let id = v.getAttribute('data-id');
        formData.append('items[]', id);
    });

    let url = type === 'tax'? '/admin/sort/terms' : '/admin/sort/taxonomy';
    HTTP.post(url, formData)
        .then(response => {
        })
        .catch(e => {
            console.log('sort error');
        });
}

function updatePageSort(){
    let items = document.querySelectorAll('.page-item');
    let formData = new FormData;
    items.forEach((v) => {
        let id = v.getAttribute('data-id');
        formData.append('pages[]', id);
    });

    let url = '/admin/pages/sort';
    HTTP.post(url, formData)
        .then(response => {
        })
    .catch(e => {
        console.log('sort error');
    });
}

function updateGalleryImageSort(){

    let items = document.querySelectorAll('.gallery-item');
    let formData = new FormData;
    items.forEach((v) => {
        let id = v.getAttribute('data-id');
        formData.append('gallery_items[]', id);
    });

    let url = '/admin/sort/gallery';
    HTTP.post(url, formData)
        .then(response => {
        })
    .catch(e => {
        console.log('sort error');
    });
}

function updateShippingMethodsSort(){
    let items = document.querySelectorAll('.shipping-method-group');
    let formData = new FormData;
    items.forEach((v) => {
        let id = v.getAttribute('data-id');
        formData.append('items[]', id);
    });

    let url = '/admin/shoppe-settings/sort/shipping-methods';
    HTTP.post(url, formData)
        .then(response => {
        })
    .catch(e => {
        console.log('sort error');
    });
}

function snackbar(type, message){
	var x = document.getElementById("snackbar");
	x.innerHTML = message;
  	x.className = "show "+type;
  	setTimeout(function(){ x.className = x.className.replace("show "+type, ""); }, 3000);
}

var viewportWidth = window.innerWidth;

function getViewportWidth(){
	viewportWidth = window.innerWidth;
	//console.log(viewportWidth);
}

function enableNestedSort(){
	let nested = $('.nested');
	nested.each(function(i,el){
		//console.log('nested');
		let nestedSort = Sortable.create(el, {
			//handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			ghostClass: 'yellow-background-class',
			group: 'shared',
			onEnd: function (e) {

			},
			onAdd: function (e) {
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
			fallbackOnBody: true,
			swapThreshold: 0.65
		});
	});
}

function showFMstandalone(){
	fm.standaloneMode = true;
	fm.selectionMode = false;
	fm.callback = false;
	fm.fileType = 'all';
	fm.boot();
	fm.showFileManager();
}

function showFMinput(inputId, previewId, type, multiple){
	fm.standaloneMode = false;
	fm.selectionMode = true;
	fm.multiple = multiple;
	fm.inputId = inputId;
	fm.previewId = previewId;
	fm.fileType = type;
	fm.callback = false;
	fm.boot();
	fm.showFileManager();
}

function showFMeditor(type){
	fm.fileType = type;
	fm.standaloneMode = false;
	fm.selectionMode = true;
	fm.callback = true;
	fm.multiple = false;
	fm.boot();
	fm.showFileManager();
}

function showFMinputGallery(previewId, type, multiple){
    fm.standaloneMode = false;
    fm.selectionMode = true;
    fm.multiple = true;
    fm.previewId = previewId;
    fm.fileType = type;
    fm.callback = false;
    fm.isGallery = true;
    fm.galleryItems = [];
    fm.boot();
    fm.showFileManager();
}

window.insertGalleryImagesCallback = function(galleryItems){
    let galleryImagesList = document.querySelector('.gallery-images-list');
    galleryItems.forEach( (image, i) => {
        let id = ID();
        let sortHandle = '<span class="gal-sort-handle"><i class="fal fa-sort"></i></span>';
        let img = '<div class="gallery-image-item"><img src="'+image.url+'" alt="gallery image"><input type="hidden" name="gallery_items['+id+'][image]" value="'+image.url+'"></div>';
        let title = '<div class="form-grid-col"><label for="title-'+id+'">Title</label><div class="input-grid-col"><input id="title-'+id+'" name="gallery_items['+id+'][title]" type="text"></div></div>';
        let caption = '<div class="form-grid-col"><label for="caption-'+id+'">Caption</label><div class="input-grid-col"><textarea id="caption-'+id+'" name="gallery_items['+id+'][caption]"></textarea></div></div>';
        let featured = '<div class="form-grid-col"><label for="featured-'+id+'">Featured</label><div class="input-grid-col"><label><input id="featured-'+id+'" name="gallery_items['+id+'][featured]" type="checkbox"> Yes</label></div></div>';
        let remove = '<div class="remove-gallery-item"><a class="remove-gallery-item-btn" data-id="'+id+'" href="#">&times;</a></div>'

        let html = '<li class="gallery-item" data-id="'+id+'">';
            html += sortHandle;
            html += img;
            html += title;
            html += caption;
            html += featured;
            html += remove;
        html += '</li>';

        $('.gallery-images-list').prepend(html);
    });
}

function addVariationAttribue(el){
    let $this = $(el);
    let id = $this.attr('data-id');
    let label = $('#attr-label-'+id+' span').html();

    let attrValues = document.querySelectorAll('.attr-value-'+id);
    let options = [];

    attrValues.forEach(function(v){
        options.push(v.value);
    });

    let $variations = $('.variation-options');

    $variations.each(function(i,v){
        let did = $(this).attr('data-variation-options-id');
        let existing = $(this).find('[data-select-id="'+id+'"]');

        let html = '<div id="product-variation-dropdown-'+id+'" class="product-variation-dropdown">';
                html += '<label for="var-select-'+id+did+'">'+label+'</label>';
            html += '<div class="select-wrapper">';
                html += '<select data-select-id="'+id+'" name="attributes['+did+']['+label+']">';

                    options.forEach(function(val){
                        html += '<option value="'+val+'">'+val+'</option>';
                    });

                html += '</select>';
            html += '</div>';
        html += '</div>';

        if( !existing.length ){
            $(this).find('.inner').prepend(html);
        }
    });

}

function removeVariationAttribue(el){
    let $this = $(el);
    let id = $this.attr('data-id');
    $('[data-select-id="'+id+'"]').parent().parent().remove();
}

function removeVariationAttribueValue(el){
    let $this = $(el);
    let id = $this.attr('data-id');
    let selects = $('[data-select-id="'+id+'"]');
    selects.each(function(i){
        Array.prototype.forEach.call( selects[i].options, function(v,j){
            if( selects[i].options[j].value === el.value ){
                selects[i].options[j].remove();
            }

            if( selects[i].options.length === 0 ){
                $(selects[i]).parent().parent().remove();
            }

        });
    });
}

function addVariationAttribueValue(el){
    let $this = $(el);
    let id = $this.attr('data-id');

    let selects = $('[data-select-id="'+id+'"]');
    selects.each(function(i){
        let hasValue = false;
        Array.prototype.forEach.call( selects[i].options, function(v,j){
            if( selects[i].options[j].value === el.value ){
                hasValue = true;
            }
        });

        if( !hasValue ){
            let option = document.createElement("option");
            option.text = el.value;
            option.value = el.value;
            selects[i].add(option);
        }
    });
}

function enableConditionals(toggleSets){
    toggleSets.forEach( (obj) => {
        obj.watcher.addEventListener( 'change', (e) => {
            let value = e.target.value;
            switch(obj.type){
                case 'checkbox':
                    obj.elsToToggle.forEach( (ob) => {
                        if ( e.target.checked ){
                            ob.target.classList.remove('hide');
                        } else {
                            ob.target.classList.add('hide');
                        }
                    });
                break;
                case 'radio':
                    obj.elsToToggle.forEach( (ob) => {
                        if ( e.target.checked && ob.value === value ){
                            ob.target.classList.remove('hide');
                        } else {
                            ob.target.classList.add('hide');
                        }
                    });
                break;
                case 'select-one':
                    obj.elsToToggle.forEach( (ob) => {
                        if ( ob.value === value ){
                            ob.target.classList.remove('hide');
                        } else {
                            ob.target.classList.add('hide');
                        }
                    });
                break;
            }

        });

        // Apply current conditional values

        let value = obj.watcher.value;
        switch(obj.type){
            case 'checkbox':
                obj.elsToToggle.forEach( (ob) => {
                    if ( obj.watcher.checked ){
                        ob.target.classList.remove('hide');
                    } else {
                        ob.target.classList.add('hide');
                    }
                });
            break;
            case 'radio':
                obj.elsToToggle.forEach( (ob) => {
                    if ( obj.watcher.checked && ob.value === value ){
                        ob.target.classList.remove('hide');
                    } else {
                        ob.target.classList.add('hide');
                    }
                });
            break;
            case 'select-one':
                obj.elsToToggle.forEach( (ob) => {
                    if ( ob.value === value ){
                        ob.target.classList.remove('hide');
                    } else {
                        ob.target.classList.add('hide');
                    }
                });
            break;
        }
    });
}

function expireEdit(){
    if( typeof object_user_edit !== 'undefined' ){
        let formData = new FormData;
        formData.append('object_user_edit', JSON.stringify(object_user_edit) );

        HTTP.post('/admin/heartbeat/expire-edit', formData)
        .then(response => {

        })
        .catch(e => {
        });
    }
}

function showObjectEdits(edits){

    let objs = document.querySelectorAll('.object-editing');

    edits.forEach( (v) => {
        objs.forEach( (el) => {
            let attrId = el.getAttribute('data-editing-object-id')
            let objectId = parseInt(attrId);
            if( v.object_id === objectId ){
                el.classList.remove('hide');
                el.innerHTML = v.user.name+' is currently editing.';
            } else {
                el.classList.add('hide');
            }
        });

    });

    if( !edits.length ){
        objs.forEach( (el) => {
            el.classList.add('hide');
        });
    }
}

window.showMediaDialog = function(type){
    document.querySelector('.media-dialog-overlay').classList.remove('hide');
    if( type === 'image' ){
        document.querySelector('.media-dialog.image').classList.remove('hide');
    }
}

window.closeMediaDialog = function(){

    document.querySelector('.media-dialog-overlay').classList.add('hide');
    document.querySelector('.media-dialog').classList.add('hide');

    document.getElementById('media-image-dialog-url').value = '';
    document.getElementById('media-image-dialog-alt-text').value = '';
    document.getElementById('image-dialog-preview').innerHTML = '';

}

window.addEventListener('DOMContentLoaded', (e) => {

	let dashboard = document.querySelector('.dashboard');
	if(dashboard){

	}

    let $closeMediaDialog = document.querySelectorAll('.close-media-dialog');
    if( $closeMediaDialog.length ){
        $closeMediaDialog.forEach( (el) =>  {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('close dialog');
                closeMediaDialog();
            });
        });
    }

    let $openMediaManager = document.querySelector('.open-fm-button');
    if( $openMediaManager ){
        $openMediaManager.addEventListener('click', (e) => {
            e.preventDefault();
            let inputId = e.target.getAttribute('data-input-id');
            let previewId = e.target.getAttribute('data-preview-id');
            console.log(inputId, previewId);
            showFMinput(inputId, previewId, 'image', false);
        });
    }

    let $mediaDialogUseImageButton = document.querySelector('.media-dialog-use-image-button');
    if($mediaDialogUseImageButton){
        $mediaDialogUseImageButton.addEventListener('click', (e) => {
            e.preventDefault();
            blockEditor.useMediaImage();
        });
    }

    const choiceElements = document.querySelectorAll('.js-choice');
    if( choiceElements.length ){
        const choices = new Choices('.js-choice');
    }

	$('.lfm-featured-image').click(function(e){
		e.preventDefault();
		let multiple = false ;
		let inputId = 'featured-image';
		let previewId = 'featured-image-preview';
		showFMinput(inputId, previewId, 'image', multiple);
	});

    $('.lfm-gallery-image').click(function(e){
        e.preventDefault();
        let multiple = true ;
        let inputId = 'gallery-image';
        let previewId = 'gallery-image-preview';
        showFMinput(inputId, previewId, 'image', multiple);
    });

    $('.lfm-gallery-images').click(function(e){
        e.preventDefault();
        let multiple = true ;
        let previewId = 'gallery-images-list';
        showFMinputGallery(previewId, 'image', multiple);
    });

	$('.lfm-social-image').click(function(e){
		e.preventDefault();
		let multiple = false ;
		let inputId = $(this).attr('data-input');
        let previewId = $(this).attr('data-preview');
        //console.log(inputId, previewId);
		showFMinput(inputId, previewId, 'image', multiple);
	});

	$('.lfm-avatar').click(function(e){
		e.preventDefault();
		let multiple = false ;
		let inputId = $(this).attr('data-input');
		let previewId = $(this).attr('data-preview');
		showFMinput(inputId, previewId, 'image', multiple);
	});

	let $backupEls = document.querySelectorAll('[data-fm-backup]');
	if( $backupEls ){
		$backupEls.forEach(function(v){

			let object_id = document.getElementById('object-id').value;
			let object_type = document.getElementById('object-type').value;

			setTimeout(function(){
				setInterval(function(){
					let content = tinymce.get('content').getContent();
					let formData = new FormData;
					formData.append('content', content);
					formData.append('object_id', object_id);
					formData.append('object_type', object_type);

					HTTP.post('/admin/object-backup', formData)
					.then(response => {
						let backup = response.data.backup;
						if( response.data.created ){
							let html = '<li><a href="#" data-backup-id="'+backup.id+'" role="button">Backup - '+backup.updated_at+'</a></li>';
							$('.restore-content-list').prepend(html);
						}
					})
					.catch(e => {
						console.log('backup error');
                        console.log(e);
					});

				}, 15000);
			}, 2000);

		});
	}

    let activityTable = document.getElementById('activity-log-table');
    let activityTab = document.getElementById('activity-log-tab');

    if( activityTable ){
        var table = new Tabulator("#activity-log-table", {
            paginationSize: 50,
            ajaxProgressiveLoad: 'load',
            ajaxProgressiveLoadDelay: 200,
            layout:"fitColumns",

            columns:[
                {"title":"Package", field:"package"},
                {"title":"Group", field:"group"},
                {"title":"Activity", field:"activity"},
                {"title":"User", field:"user", align:"center"},
                {"title":"Level", field:"level", align: 'center', sorter: 'number'},
                {"title":"Created On", field:"created_on",
                    sorter: 'datetime'
                    , sorterParams:{
                        format: 'MM-DD-YY h:mm a'
                    }
                }
            ]
        });

        activityTab.addEventListener('click', (el) => {
            table.setData('/admin/settings/activity/log');
        });
    }

    $('.view-pages-by-select').change( function(e) {
        let val = $(this).val();
        let year = new Date().getFullYear() + 20;
        document.cookie = "view_pages_by="+val+"; expires=Thu, 18 Dec "+year+" 12:00:00 UTC; path=/";
        location.reload();
    });

    setInterval(function(){
        let formData = new FormData;
        formData.append('beep', 'bop');
        if( typeof object_user_edit !== 'undefined' ){
            formData.append('object_user_edit', JSON.stringify(object_user_edit) );
        }
        if( typeof object_type !== 'undefined' ){
            formData.append('object_type', object_type );
        }
        HTTP.post('/admin/heartbeat', formData)
            .then( response => {
                let edits = response.data.edits;

                showObjectEdits(edits);

            })
            .catch(e => {
                console.log('--Heartbeat error--');
                console.log(e);
                if(e.response.status === 419){
                    $('.expired-csrf-modal').show();
                }
            });
    }, 5000);

    window.addEventListener('beforeunload', () => {
        if( typeof object_user_edit !== 'undefined' ){
            expireEdit();
        }
    });

    $('.shipping-method-type').change(function(){
        let checked = $('.shipping-method-type:checked').val();
        if( checked === 'flat' ){
            $('.flat-rate-shipping-fields').removeClass('hide');
            $('.estimated-rate-shipping-fields').addClass('hide');
            $('.free-shipping-fields').addClass('hide');
        } else if ( checked === 'estimated' ) {
            $('.flat-rate-shipping-fields').addClass('hide');
            $('.estimated-rate-shipping-fields').removeClass('hide');
            $('.free-shipping-fields').addClass('hide');
        } else if ( checked === 'free' ){
            $('.flat-rate-shipping-fields').addClass('hide');
            $('.estimated-rate-shipping-fields').addClass('hide');
            $('.free-shipping-fields').removeClass('hide');
        }
    });

    $('.close-csrf-modal-action').click(function(e){
        e.preventDefault();
        $('.expired-csrf-modal').hide();
    });

	$('.restore-content-btn').click(function(e){
		e.preventDefault();
		$('.restore-content-modal').toggle();
	});

	$('.restore-content-list').on('click', 'a',function(e){
		e.preventDefault();
		let id = $(this).attr('data-backup-id');
		HTTP.get('/admin/object-backup/'+id)
		.then(response => {
			let backup = response.data.backup;
			tinymce.get('content').setContent(backup.content);
			$('.restore-content-modal').hide();
		})
		.catch(e => {
			console.log('backup error');
		});
	});

    $('.product-attr').change(function(){
        let id = $(this).attr('data-id');
        let label = $('#attr-label-'+id+' span').html();
        let attrValues = document.querySelectorAll('.attr-value-'+id);

        if( $(this).is(':checked') ){
            attrValues.forEach(function(v){
                v.checked = true;
            });
        }

        if( $(this).is(':checked') ){
            $('#attr-values-'+id).addClass('open');
            addVariationAttribue(this);
        } else {
            $('#attr-values-'+id).removeClass('open');
            removeVariationAttribue(this);
        }
    });

    $('.attr-value').change(function(){
        let id = $(this).attr('data-id');
        let value = this.value;
        let checks = document.querySelectorAll('.attr-value-'+id);
        if( $(this).is(':checked') ){
            addVariationAttribueValue(this);
        } else {
            removeVariationAttribueValue(this);
        }

        let anyChecked = false;
        checks.forEach(function(v){
                if( v.checked ){
                    anyChecked = true;
                }
            });

        let par = document.getElementById('product-attr-'+id);
            if( !anyChecked ){
                par.checked = false;
            } else {
                par.checked = true;
            }

    });

    $('.add-variation-btn').click(function(e){
        e.preventDefault();
        let id = ID();
        let html = '';
        let productAttrChecked = false;
        chosenAttributes = [];

        // Check to see if any attributes have been checked
        let productAttrs = document.querySelectorAll('.product-attr');
        productAttrs.forEach(function(v){
            if( v.checked ){
                productAttrChecked = true;
            }
        });

        if( !productAttrChecked ){
            snackbar('warning', 'Please choose at least one product attribute.');
            return false;
        }

        let $productAttr = $('.product-attr');
        $productAttr.each(function(i,v){
           let id = $(this).attr('data-id');
           let label = $('#attr-label-'+id+' span').html();
           let attrValues = document.querySelectorAll('.attr-value-'+id);
           let options = [];

           attrValues.forEach( function(v){
                   if( v.checked ){
                       options.push(v.value);
                   }
               });

           let obj = {
                   id: id,
                   label: label,
                   options: options
               };

            chosenAttributes.push(obj);
        });



        html += '<div class="variation-item" data-variation-id="'+id+'" data-id="'+id+'">';
            html += '<a href="#" class="delete-variation-btn" data-id="'+id+'">&times;</a>';

            html += '<div class="variation-options" data-variation-options-id="'+id+'">';
                html += '<div class="inner">';
                    chosenAttributes.forEach(function(v){

                       html += '<div id="product-variation-dropdown-'+v.id+'" class="product-variation-dropdown">';
                           html += '<label for="var-select-'+v.id+id+'">'+v.label+'</label>';
                           html += '<input type="hidden" name="variation_attributes['+id+']['+v.id+'][attribute_id]" value="'+v.id+'">';
                           html += '<input type="hidden" name="variation_attributes['+id+']['+v.id+'][attribute]" value="'+v.label+'">';
                           html += '<div class="select-wrapper">';
                                html += '<select id="var-select-'+v.id+id+'" data-select-id="'+v.id+'" name="variation_attributes['+id+']['+v.id+'][value]">';
                                v.options.forEach(function(val){
                                   html += '<option value="'+val+'">'+val+'</option>';
                                });
                                html += '</select>';
                           html += '</div>';
                       html += '</div>';

                    });

                html += '<div class="variation-collapse">';
                    html += '<a href="#variation-fields'+id+'" class="variation-collapse-btn open" data-variation-collapse-id="'+id+'"><i class="fal fa-angle-right"></i></a>';
                html += '</div>';

                html += '</div>';
            html += '</div>'; // Variation options

            html += '<div id="variation-fields-'+id+'" class="variation-fields open">';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="image'+id+'">Image</label>';
                    html += '<div class="input-col row">';

                        html += '<div class="input-group">';
                            html += '<span class="input-group-btn">';
                            html += '<a class="lfm-image" data-lfm data-input="lfm-'+id+'" data-preview="lfm-preview-'+id+'">';
                                html += '<i class="fas fa-image"></i> Choose Image';
                            html += '</a>';
                            html += '</span>';
                            html += '<input id="lfm-'+id+'" class="file-list-input" data-lfm-input value="" type="text" name="variations['+id+'][image]">';
                        html += '</div>';


                        html += '<div id="lfm-preview-'+id+'" data-lfm-holder class="lfm-image-preview">';
                        //html += strlen($filename)? '<a href="#" role="button" data-preview-id="lfm-preview-'+id+'" data-input-id="lfm-'+id+'" class="clear-lfm-image">&times;</a><img class="repeater-image-preview" src="'.$filename.'">' : '';
                        html += '</div>';

                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="desc'+id+'">Description</label>';
                    html += '<div class="input-col">';
                        html += '<textarea id="desc'+id+'" class="smaller" name="variations['+id+'][desc]" value=""></textarea>';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="cost'+id+'">Cost</label>';
                    html += '<div class="input-col">';
                        html += '<input type="number" id="cost'+id+'" name="variations['+id+'][cost]" value="" step="0.01">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="price'+id+'">Price</label>';
                    html += '<div class="input-col">';
                        html += '<input type="number" id="price'+id+'" name="variations['+id+'][price]" value="" step="0.01">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="sale-price'+id+'">Sale Price</label>';
                    html += '<div class="input-col">';
                        html += '<input type="number" id="sale-price'+id+'" name="variations['+id+'][sale_price]" value="" step="0.01">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="sku'+id+'">SKU</label>';
                    html += '<div class="input-col">';
                        html += '<input type="text" id="sku'+id+'" name="variations['+id+'][sku]" value="">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="mfg-part-number'+id+'">MFG Part Number</label>';
                    html += '<div class="input-col">';
                        html += '<input type="text" id="mfg-part-number'+id+'" name="variations['+id+'][mfg_part_number]" value="">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="stock'+id+'">Stock</label>';
                    html += '<div class="input-col">';
                        html += '<input type="number" id="stock'+id+'" name="variations['+id+'][stock]" value="">';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="weight'+id+'">Weight</label>';
                    html += '<div class="input-col input-col-group">';
                        html += '<div><input type="number" id="weight'+id+'" name="variations['+id+'][weight]" value="" step="0.01"> lbs</div>';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="dim'+id+'">Dimensions</label>';
                    html += '<div class="input-col input-col-group">';
                        html += '<div><input type="number" id="width'+id+'" name="variations['+id+'][width]" value="" placeholder="Width" step="0.01"> in.</div>';
                        html += '<div><input type="number" id="height'+id+'" name="variations['+id+'][height]" value="" placeholder="Height" step="0.01"> in.</div>';
                        html += '<div><input type="number" id="depth'+id+'" name="variations['+id+'][depth]" value="" placeholder="Depth" step="0.01"> in.</div>';
                    html += '</div>';
                html += '</div>';

                html += '<div class="form-row">';
                    html += '<label class="label-col" for="shipping-class'+id+'">Shipping Class</label>';
                    html += '<div class="input-col">';
                        html += '<div class="select-wrapper">';
                            html += '<select name="variations['+id+'][shipping_class_id]" id="shipping-class'+id+'">';
                                html += '<option value="same_as_parent">Same as Parent</option>';
                                shipping_classes.forEach( (v) => {
                                    html += '<option value="'+v.id+'">'+v.title+'</option>';
                                });
                            html += '</select>';
                        html += '</div>';
                    html += '</div>';
                html += '</div>';

            html += '</div>'; // Variation fields

        html += '</div>';

        $('.variations-list').append(html);

    });

    $('.variations-list').on('click', '.variation-collapse-btn', function(e){
        e.preventDefault();
        let id = $(this).attr('data-variation-collapse-id');
        if( $(this).hasClass('open') ){
            $(this).removeClass('open');
            $('#variation-fields-'+id).removeClass('open');
        } else {
            $(this).addClass('open');
            $('#variation-fields-'+id).addClass('open');
        }
    });

    $('.variations-list').on('click', '.delete-variation-btn', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');

        Swal.fire({
              "title": 'Delete',
              text: "Are you sure you want to delete this?",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#333',
              cancelButtonColor: '#ccc',
              confirmButtonText: 'Yes, delete!'
        }).then(function (isConfirm) {
            if(isConfirm.dismiss !== 'cancel'){

                let formData = new FormData();
                formData.append('_method', 'delete');
                formData.append('id', id);

                HTTP.post('/admin/product-variation-delete', formData)
                    .then(response => {
                        $('[data-variation-id="'+id+'"]').remove();
                    })
                    .catch(e => {
                        console.log('delete error');
                    });

            }
        });

    });

    $('.variations-list').on('click', '.choose-gallery-images', function(e){
        e.preventDefault();
        showFMinput('gallery-image', 'gallery-image-preview', 'image', true);
    });

    $('.variations-list').on('click', '.choose-gallery-images', function(e){
        e.preventDefault();
        showFMinput('gallery-image', 'gallery-image-preview', 'image', true);
    });

    $('#product-shipping-rate-type').change(function(){
        if( $(this).val() === 'flat' ){
            $('#shipping-rate-row').show();
        } else {
            $('#shipping-rate-row').hide();
            $('#shipping-rate').val('');
        }
    });

    $('#product-type').change(function(){
        if( $(this).val() === 'download' ){
            $('#product-file-row').show();
            $('#product-role-row').hide();
            $('#product-subscription-row').hide();
        } else if ( $(this).val() === 'subscription' ) {
            $('#product-role-row').hide();
            $('#product-file-row').hide();
            $('#taxable').attr('checked', true);
            $('#product-subscription-row').show();
        } else if ( $(this).val() === 'role' ) {
            $('#product-role-row').show();
            $('#product-file-row').hide();
            $('#product-subscription-row').hide();
        } else {
            $('#product-file-row').hide();
            $('#product-role-row').hide();
            $('#product-subscription-row').hide();
        }
    });

    $('#subscription-id').change( (e) => {
        let opt = e.target.options[e.target.selectedIndex];
        let amount = opt.getAttribute('data-amount');
        document.getElementById('price').value = amount;
    });

	$('#item-file').click(function(e){
		e.preventDefault();
		let multiple = false ;
		let inputId = $(this).attr('data-input');
		showFMinput(inputId, '', 'file', multiple);
	});

	$('body').on('click', '.lfm-image', function(e){
		e.preventDefault();
		let multiple = parseInt($(this).attr('data-multiple')) === 1 ? true : false ;
		let inputId = $(this).attr('data-input');
		let previewId = $(this).attr('data-preview');
		showFMinput(inputId, previewId, 'image', multiple);
	});

	$('body').on('click', '.lfm-file',function(e){
		e.preventDefault();
		let multiple = parseInt($(this).attr('data-multiple')) === 1 ? true : false ;
		let inputId = $(this).attr('data-input');
		showFMinput(inputId, '', 'file', multiple);
	});

	window.onresize = getViewportWidth;

	$('.menu-toggle').click(function(e){
		e.preventDefault();
		$('.sidebar-nav-group').removeClass('toggled');
		$('body').toggleClass('menu-open');

		if( $('body').hasClass('menu-open') ){

    		$('.menu-toggle i').removeClass('fa-long-arrow-right');
    		$('.menu-toggle i').addClass('fa-long-arrow-left');
		} else {
    		$('.menu-toggle i').removeClass('fa-long-arrow-left');
    		$('.menu-toggle i').addClass('fa-long-arrow-right');
		}


		if(viewportWidth < 951){
			document.cookie = "menu_open=Y; expires=Thu, 18 Dec 1999 12:00:00 UTC; path=/";
		} else {
			if( $('body').hasClass('menu-open') ){
				document.cookie = "menu_open=Y; expires=Thu, 18 Dec 2040 12:00:00 UTC; path=/";
			} else {
				document.cookie = "menu_open=N; expires=Thu, 18 Dec 2040 12:00:00 UTC; path=/";
			}
		}

		window.dispatchEvent(new Event('resize'));
	});

	$('.toggle-file-manager').click(function(e){
		e.preventDefault();
		fm.boot();
		showFMstandalone();
	});

	$('.stats-view-toggle').click(function(e){
		e.preventDefault();
		$('#view-selector-container').toggle();
	})

	$('.menu-dropdown-toggle').click(function(){
		$('.sidebar-nav-group').toggleClass('toggled');
	});

	$("#media-file").change(function() {
  		readURL(this);
	});

	$('.typeahead').on('typeahead:selected', function(evt, item) {
	    //console.log(item);
		chosenFeatured = item;
		$('.typeahead').val(item.name);

	});

	$('.form-fields-list').on('click', '[data-conditional-toggle]', function(){
    	let id = $(this).attr('data-conditional-toggle');
    	if( $(this).is(':checked') ){
        	$('#conditional-toggle-'+id).show();
    	} else {
        	$('#conditional-toggle-'+id).hide();
    	}
	});

	$('.form-tabs li a').click(function(e){
    	e.preventDefault();
    	let id = $(this).attr('href');
    	$(this).parent().parent().find('a').removeClass('active');
    	$(this).addClass('active');
    	$(this).parent().parent().siblings('.tabs-content').find('.tab-content').removeClass('active');
    	$(id).addClass('active');
	});

	/*$('.typeahead').typeahead({
			hint: true,
		    highlight: true,
		    minLength: 2
		},{
			display: 'name',
			limit: 20,
			source: engine.ttAdapter(),
			name: 'productsList',
			templates: {
		        empty: [
		                '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
		        ],
		        header: [
		            '<div class="list-group search-results-dropdown">'
		        ],
		        suggestion: function (data) {
					console.log(data);
		                return '<div class="list-group-item">' + data.name + '</div>'
				}
		    }
	});*/

	$('#setting-type').change(function(){
		let v = $(this).val();
		$('.setting-type-field').hide();
		if( v === 'BOOL' ){
			$('#setting-type-bool').show();
		}
		if(v === 'STRING' ){
			$('#setting-type-string').show();
		}
	});

	$('form').submit(function(e){
		let requiredMissing = false;

        let $required = e.target.querySelectorAll("[required]");

		$required.forEach(function(v){
			if( v.value === '' ){
				requiredMissing = true;
			}
		});

		if( requiredMissing ){
			snackbar('warning', 'Please fill in all required fields.');
		}
	});

	var sortList = document.querySelector('.fields-list');
	var sortList2 = document.querySelector('.form-fields-list');
	var fieldList = document.querySelector('.choose-field-list');
	let repeaterList = document.querySelector('.repeater-fields-list');

	if(sortList){
		var sortable = Sortable.create(sortList, {
			handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			group: 'shared',
			onEnd: function (e) {
				updateGroupSort();
			},
			onAdd: function (e) {
				//console.log('ADD',e);
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
		});

	}

	let repeaterGroupList = document.querySelector('.repeater-fields-group');
	if(repeaterGroupList){
		Sortable.create(repeaterGroupList, {
			handle: '.repeater-sort-handle',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			onEnd: function (e) {
				updateObjectRepeaterSort();
			},
			onAdd: function (e) {
				//console.log('ADD',e);
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
		});
	}

    let termList = document.querySelectorAll('.term-list');
    let pageList = document.querySelectorAll('.sort-pages-list');
    let pageChildrenList = document.querySelectorAll('.sort-pages-children-list');
    let taxTypeTable = document.querySelector('.tax-type-table');
    if(termList.length){
        termList.forEach( (el) => {
            Sortable.create(el, {
                handle: '.tax-sort-handle',
                easing: "cubic-bezier(1, 0, 0, 1)",
                animation: 150,
                group: 'nested',
                swapThreshold: 0.65,
                onEnd: function (e) {
                    updateTaxSort('tax');
                },
                onAdd: function (e) {
                },
                onStart: function (evt) {
                    evt.oldIndex;
                },
            });
        });
    }

    if(pageList.length){
        pageList.forEach( (el) => {
            Sortable.create(el, {
                easing: "cubic-bezier(1, 0, 0, 1)",
                animation: 150,
                group: 'nested',
                swapThreshold: 0.65,
                onEnd: function (e) {
                    updatePageSort();
                },
                onAdd: function (e) {
                },
                onStart: function (evt) {
                    evt.oldIndex;
                },
            });
        });
    }

    let termChlidren = document.querySelectorAll('.term-children');

    if(termChlidren.length){
        termChlidren.forEach( (el, i) => {
            Sortable.create(el, {
                handle: '.tax-sort-handle',
                easing: "cubic-bezier(1, 0, 0, 1)",
                animation: 150,
                group: 'nested-'+i,
                swapThreshold: 0.65,
                onEnd: function (e) {
                    updateTaxSort('tax');
                },
                onAdd: function (e) {
                },
                onStart: function (evt) {
                },
            });
        });
    }

    if(pageChildrenList.length){
        pageChildrenList.forEach( (el, i) => {
            Sortable.create(el, {
                easing: "cubic-bezier(1, 0, 0, 1)",
                animation: 150,
                group: 'nested-'+i,
                swapThreshold: 0.65,
                onEnd: function (e) {
                    updatePageSort();
                },
                onAdd: function (e) {
                },
                onStart: function (evt) {
                },
            });
        });
    }

    if(taxTypeTable){
        Sortable.create(taxTypeTable, {
            handle: '.tax-sort-handle',
            easing: "cubic-bezier(1, 0, 0, 1)",
            animation: 150,
            swapThreshold: 0.65,
            onEnd: function (e) {
                //updateObjectRepeaterSort();
                updateTaxSort('taxtype');
            },
            onAdd: function (e) {
                //console.log('ADD',e);
            },
            onStart: function (evt) {
                evt.oldIndex;
            },
        });
    }

    let $galleryImagesList = document.querySelector('.gallery-images-list');
    if( $galleryImagesList ){
       Sortable.create($galleryImagesList, {
            handle: '.gal-sort-handle',
            easing: "cubic-bezier(1, 0, 0, 1)",
            animation: 150,
            swapThreshold: 0.65,
            onEnd: function (e) {
                updateGalleryImageSort();
            },
            onAdd: function (e) {
            },
            onStart: function (evt) {
                evt.oldIndex;
            },
        });
    }

    let $shippingMethodsGrouplist = document.querySelector('.shipping-methods-group-list');
    if( $shippingMethodsGrouplist ){
        Sortable.create($shippingMethodsGrouplist, {
            handle: '.shipping-method-rows',
            easing: "cubic-bezier(1, 0, 0, 1)",
            animation: 150,
            swapThreshold: 0.65,
            onEnd: function (e) {
                updateShippingMethodsSort();
            },
            onAdd: function (e) {
            },
            onStart: function (evt) {
                evt.oldIndex;
            },
        });
    }

	$('.repeater-fields-group').on('click', '.repeater-group-toggle', function(e){
		e.preventDefault();
		$(this).toggleClass('open');
		$(this).parent().next('.repeater-fields-group-row').toggleClass('open');
	});

	$('.repeater-fields-group').on('click', '.delete-repeater-row', function(e){
		e.preventDefault();
		let id = $(this).attr('data-repeater-row-id');
		let $this = $(this);
		if( id.length ){
			let formData = new FormData;
			formData.append('_method', 'delete');
			HTTP.post('/admin/custom-fields/field-group-remove/'+id, formData)
			.then(response => {
				$this.parent().parent().remove();
			})
			.catch(e => {
				console.log('delete error');
			});

		} else {
			$this.parent().parent().remove();
		}
	});

	if(sortList2){
		var sortable3 = Sortable.create(sortList2, {
			handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			group: 'shared',
			onEnd: function (e) {
				updateFieldSort();
			},
			onAdd: function (e) {
				///console.log('ADD',e);
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
		});
	}

	if(repeaterList){
		let sortable2 = Sortable.create(repeaterList, {
			handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			group: 'shared',
			onEnd: function (e) {
				updateGroupSort();
			},
			onAdd: function (e) {
				//console.log('ADD',e);
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
		});
		enableNestedSort();
	}

	var menuItemsDrag = document.querySelector('.menu-items-drag');
	var menuItemsDrop = $('.current-menu-items ul');
	if(menuItemsDrag){

		var itemsDrop = Sortable.create(menuItemsDrop, {
			//handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			group: 'shared',
			onEnd: function (e) {

			},
			onAdd: function (e) {
			},
			onStart: function (evt) {
				evt.oldIndex;
			},
		});
	}


	$('.field-type').click(function(e){
		e.preventDefault();
		let type = $(this).attr('data-type');
		addFieldType(type, 0);
	});

	$('body').on('click', '.clear-featured-image', function(e){
		e.preventDefault();
		$('#featured-image').val('');
		$('#featured-image-preview').html('');
	});

	$('body').on('click', '.clear-lfm-image', function(e){
		e.preventDefault();
		let $inputId = $(this).attr('data-input-id');
		let $previewId = $(this).attr('data-preview-id');
		$('#'+$inputId).val('');
		$('#'+$previewId).html('');
	});

    $('body').on('click', '.clear-lfm-gallery-image', function(e){
        e.preventDefault();
        let id = $(this).attr('data-gallery-image-id');
        $('input[data-gallery-image-id="'+id+'"]').remove();
        $('div[data-gallery-image-id="'+id+'"]').remove();
    });

	$('body').on('click', '.clear-social-image', function(e){
		e.preventDefault();
		$('#social-image').val('');
		$('#social-image-preview').html('');
	});

	$('#item-type').change(function(){
		let type = $(this).val();
		if( type.length ){
			$('.menu-item-type').hide();
			$('#item-'+type+'-group').css('display', 'flex');
			if( type === 'taxonomy_term' ){
				$('#item-taxonomy-id-group').css('display', 'flex');
			}
		}
	});

	$('#item-taxonomy-id').change(function(){
		if( $('#item-type').val() === 'taxonomy_term' && $(this).val() !== '' ){
			let tax = $(this).val();

			let formData = new FormData();
			formData.append('id', $(this).val());

			HTTP.post('/admin/menu-items/taxonomy-terms', formData)
			.then(response => {
				let list = '';
				if( response.data.terms.length > 0 ){
					response.data.terms.forEach(function(v){
						list += '<option value="'+v.url+'">'+v.title+'</option>';
					});
					$('#item-taxonomy-term').attr('disabled', false);
					$('#item-taxonomy-term').html(list);
				}
			})
			.catch(e => {
				console.log('delete error');
			});
		}
	})

	let mainMenuDrop;
	menuItemsDrop.each(function(i,v){
		mainMenuDrop = Sortable.create(v, {
			//handle: '.field-sort',
			easing: "cubic-bezier(1, 0, 0, 1)",
			animation: 150,
			ghostClass: 'blue-background-class',
			group: 'shared',
			onEnd: function (e) {
				updateMenuItems();
			},
			onAdd: function (e) {

			},
			onStart: function (evt) {
				evt.oldIndex;
			},
			fallbackOnBody: true,
			swapThreshold: 0.85
		});
	});

	$('#add-menu-item-btn').click(function(e){
		e.preventDefault();
		let type = $('#item-type').val();
		let text = $('#item-name').val();
		if( text.length === 0 ){
			return false;
		}
		let target = $('#item-target').val();
		let newItem = '';
		let url = '';
		if( type.length && text.length){
			switch(type){
				case 'url' :
				url = $('#item-url').val();
				break;
				case 'page' :
				url = $('#item-page').val();
				break;
				case 'entry_type' :
				url = '/'+$('#item-entry-type').val();
				break;
				case 'entry' :
				let val = $('#item-entry').val();
				let parts = val.split(':');
				url = '/'+parts[0]+'/'+parts[1];
				break;
				case 'taxonomy' :
				url = $('#item-taxonomy').val();
				break;
				case 'taxonomy_term' :
				url = $('#item-taxonomy-term').val();
				break;
				case 'file' :
				url = $('#item-input-file').val();
				break;
			}

			if( type === 'taxonomy_term' && $('#item-taxonomy-term').val() === '' ){
				return false;
			}

			newItem = '<li data-type="'+type+'" data-target="'+target+'" data-title="'+text+'" data-url="'+url+'">'+text+'<div class="menu-url">URL: '+url+'</div><a class="remove-menu-item" href="/">&times;</a><ul class="nested"></ul></li>';

			$('.menu-items-drop').append(newItem);
			mainMenuDrop.options.onEnd(this);
			enableNestedSort();
		}
	});

	$('.menu-items-drop').on('click', '.remove-menu-item', function(e){
		e.preventDefault();
		$(this).parent().remove();
		updateMenuItems();
	});

	$('.fields-list').on('keyup', '.field-label', function(){
		let label = $(this).val();
		let idSplit = this.id.split('-');
		let id = idSplit[1];
		let name = slugger(label, {replacement: '_'});
		$('#name-'+id).val(name);
	});

	$('.fields-list').on('click', '.collapse-field-row', function(e){
		e.preventDefault();
		let id = $(this).attr('data-row-id');
		$('#field-group-'+id).toggle();
		$(this).toggleClass('close');
	});

	$('.fields-list').on('click', '.remove-field-row', function(e){
		e.preventDefault();
		let rowid = $(this).attr('data-row-id');
		let repeater = $(this).attr('data-repeater');
		let formData = new FormData;
		formData.append('id', rowid);
		formData.append('_method', 'delete');
		formData.append('repeater', repeater);

		HTTP.post('/admin/custom-fields/field', formData)
		.then(response => {
			$('#'+rowid).remove();
		})
		.catch(e => {
			console.log('delete error');
		});

	});

	$('.form-fields-list').on('keyup', '.field-label', function(){
		let label = $(this).val();
		let idSplit = this.id.split('-');
		let id = idSplit[1];
		let name = slugger(label, {replacement: '_'});
		$('#name-'+id).val(name);
	});

	$('.form-fields-list').on('click', '.collapse-field-row', function(e){
		e.preventDefault();
		let id = $(this).attr('data-row-id');
		$('#field-group-'+id).toggle();
		$(this).toggleClass('close');
	});

	$('.form-fields-list').on('click', '.remove-field-row', function(e){
		e.preventDefault();
		let rowid = $(this).attr('data-row-id');
		let current = $(this).attr('data-row-current');
		if(current){
			let formData = new FormData;
			formData.append('id', rowid);
			formData.append('_method', 'delete');

			HTTP.post('/admin/form/field', formData)
			.then(response => {
				$('#'+rowid).remove();
			})
			.catch(e => {
				console.log('delete error');
			});
		} else {
			$('#'+rowid).remove();
		}

	});

	$('.fields-list').on('click', '.fields-drop-down', function(){
		$(this).toggleClass('open');
	});

	$('.main-fields-dropdown').on('click', '.fields-drop-down', function(){
		$(this).toggleClass('open');
	});

	$('.main-fields-dropdown').on('click', '.form-fields-drop-down', function(){
		$(this).toggleClass('open');
	});

	$('.form-field-type').click(function(e){
		e.preventDefault();
		let type = $(this).attr('data-type');
		addFormFieldType(type);
	});

	$('.fields-list').on('click', '.fields-drop-down ul li', function(){
		//console.log('ADD Repeater field');
		let type = $(this).attr('data-type');
		let repeaterId = $(this).attr('data-id');
		addFieldType(type, repeaterId);
	});

	let ruleCategory = null;
	let ruleCategoryType = null;
	let ruleCategorySpecific = null;

	$('.rule-category-select').on('select2:select', function (e) {
		var data = e.params.data;
      //	console.log(data);
		if( data.id.length > 0 ){

			ruleCategory = data.id;

			$('.rule-category-type-select').select2('data', null);
			$('.rule-category-type-select').empty();
			ruleCategorySpecific = null;

			if( data.id === 'entries' ){

				$('.rule-category-type-select').prop("disabled", false);
				var newOption = new Option('Choose entry type', '', false, false);
				$('.rule-category-type-select').prepend(newOption).trigger('change');

				HTTP.get('/admin/custom-fields/entry-types')
				.then(response => {
					response.data.entry_types.forEach(function(v){
						var newOption = new Option(v.entry_type, v.slug, false, false);
						$('.rule-category-type-select').append(newOption).trigger('change');
					});
				})
				.catch(e => {
					console.log('entry types error');
				});

			}

			if( data.id === 'taxonomy' ){
				$('.rule-category-type-select').prop("disabled", false);
				var newOption = new Option('Choose taxonomy type', '', false, false);
				$('.rule-category-type-select').prepend(newOption).trigger('change');

				var newOption = new Option('All', '*', false, false);
				$('.rule-category-type-select').append(newOption).trigger('change');

				HTTP.get('/admin/custom-fields/taxonomy-types')
				.then(response => {
					response.data.taxonomy_types.forEach(function(v){
						var newOption = new Option(v.title, v.slug, false, false);
						$('.rule-category-type-select').append(newOption).trigger('change');
					});
				})
				.catch(e => {
					console.log('entry types error');
				});

				var newOption = new Option('Skip this step', '', false, false);
				$('.rule-category-specific-select').empty();
				$('.rule-category-specific-select').prepend(newOption).trigger('change');
				$('.rule-category-specific-select').prop("disabled", true);

			}

			if( data.id === 'pages' ){
				$('.rule-category-type-select').prop("disabled", true);
				var newOption = new Option('Skip this step', '', false, false);
				$('.rule-category-type-select').prepend(newOption).trigger('change');
				$('.rule-category-specific-select').empty();
				$('.rule-category-specific-select').prop("disabled", false);

				ruleCategoryType = null;
				$('.add-rule-btn').prop('disabled', false);
				var newOption = new Option('All', '*', false, false);
				$('.rule-category-specific-select').prepend(newOption).trigger('change');

				HTTP.get('/admin/custom-fields/pages')
				.then(response => {
					response.data.pages.forEach(function(v){
						var newOption = new Option(v.title, v.title+'|'+v.id, false, false);
						$('.rule-category-specific-select').append(newOption).trigger('change');
					});
				})
				.catch(e => {
					console.log('entry types error');
				});

			}

			if( data.id === 'products' ){
				$('.rule-category-type-select').prop("disabled", true);
				var newOption = new Option('Skip this step', '', false, false);
				$('.rule-category-type-select').prepend(newOption).trigger('change');
				$('.rule-category-specific-select').empty();
				$('.rule-category-specific-select').prop("disabled", false);

				ruleCategoryType = null;
				$('.add-rule-btn').prop('disabled', false);
				var newOption = new Option('All', '*', false, false);
				$('.rule-category-specific-select').prepend(newOption).trigger('change');

			}

			if( data.id === 'events' ){

			}

		} else {
			ruleCategory = null;
			$('.add-rule-btn').prop('disabled', true);
			$('.rule-category-type-select').prop("disabled", true);
		}
	});

	$('.rule-category-type-select').on('select2:select', function (e) {
		var data = e.params.data;
      	//console.log(data);
		if( data.id.length > 0 ){

			ruleCategoryType = data.id;
			$('.add-rule-btn').prop('disabled', false);
			$('.rule-category-specific-select').select2('data', null);
			$('.rule-category-specific-select').empty();

			$('.rule-category-specific-select').prop("disabled", false);

			var newOption = new Option('All', '*', false, false);
			$('.rule-category-specific-select').prepend(newOption).trigger('change');

			if( ruleCategory === 'entries' ){

				HTTP.get('/admin/custom-fields/entry-type?entry_type='+ruleCategoryType)
				.then(response => {
					response.data.entry_types.forEach(function(v){
						var newOption = new Option(v.title, v.title+'|'+v.id, false, false);
						$('.rule-category-specific-select').append(newOption).trigger('change');
					});
				})
				.catch(e => {
					console.log('entry types error');
				});

			}

			if( ruleCategory === 'pages' ){

			}

			if( ruleCategory === 'taxonomy' ){

				var newOption = new Option('Skip this step', '', false, false);
				$('.rule-category-specific-select').empty();
				$('.rule-category-specific-select').prepend(newOption).trigger('change');
				$('.rule-category-specific-select').prop("disabled", true);

			}

			if( ruleCategory === 'products' ){

			}

		} else {
			ruleCategoryType = null;
			$('.add-rule-btn').prop('disabled', true);
			$('.rule-category-specific-select').prop("disabled", true);
		}
	});

	$('.rule-category-specific-select').on('select2:select', function (e) {
		var data = e.params.data;
      	//console.log(data);
		if( data.id.length > 0 ){
			ruleCategorySpecific = data.id;
			$('.add-rule-btn').prop('disabled', false);
		} else {
			$('.add-rule-btn').prop('disabled', true);
		}
	});

	$('.rule-category-select').select2({
		minimumResultsForSearch: -1,
		width: '100%'
	});

	$('.rule-category-type-select').select2({
		width: '100%'
	});

	$('.rule-category-specific-select').select2({
		width: '100%'
	});

	$('.object-term-checkbox').change(function(){
		if( !$(this).is(':checked') ){
			let objectType = $(this).attr('data-object-type');
			let entryId = $(this).attr('data-entry-id');
			let taxonomyTypeId = $(this).attr('data-taxonomy-type');
			let termId = $(this).attr('data-term-id');

			let formData = new FormData;
			formData.append('object_type', objectType);
			formData.append('object_id', entryId);
			formData.append('taxonomy_type_id', taxonomyTypeId);
			formData.append('term_id', termId);

			HTTP.post('/admin/entry/terms/remove', formData)
			.then(response => {
			})
			.catch(e => {
				console.log('error');
			});
		}
	});

	$('.has-dropdown > a').click(function(e){
    	if( $('body').hasClass('menu-open') ){
		    e.preventDefault();
            $(this).parent().toggleClass('open');
            $(this).toggleClass('open');
            $(this).siblings('ul').toggleClass('open');
		}
	});

	$('.add-rule-btn').click(function(){

		if( !ruleCategorySpecific ){
			ruleCategorySpecific = '*';
		}

		let s = ruleCategorySpecific.split('|');
		let spec = ( s[0] === '*' )? 'All' : s[0];
		let pre = ruleCategoryType+': ';

		if( !ruleCategoryType ){
			pre = '';
		}

		let groupId = $('#group-id').length? $('#group-id').val() : 0;

		if( groupId.length ){

			let formData = new FormData;
			formData.append('group_id', groupId);
			formData.append('rule_category', ruleCategory);
			formData.append('rule_category_type', ruleCategoryType);
			formData.append('rule_category_specific', ruleCategorySpecific);

			HTTP.post('/admin/custom-fields/group-rules', formData)
			.then(response => {
				let html = '<li id="ruleid-'+response.data.id+'">';
				html += '<div>'+ruleCategory+'</div><div>'+pre+spec+'</div><div><a class="delete-custom-field-rule" data-id="'+response.data.id+'" href="/">&minus;</a></div>';
				html += '</li>';

				$('.rules-list').append(html);
			})
			.catch(e => {
				console.log('rule error');
			});

		} else {

			let html = '<li>';
			html += '<div>'+ruleCategory+'</div><div>'+pre+spec+'</div><div><a class="delete-custom-field-rule" data-id="" href="/">&minus;</a></div>';
			html += '</li>';

			let ruleSet = ruleCategory+':'+ruleCategoryType+':'+ruleCategorySpecific;

			html += '<input type="hidden" name="rules[]" value="'+ruleSet+'">';

			$('.rules-list').append(html);
		}

	});

    let $toggleTos = document.querySelectorAll('[data-toggle-to]');
    if( $toggleTos.length ){
        let toggleSets = [];
        $toggleTos.forEach( (el) => {
            let type = el.type;
            //console.log(el);
            let toggleTo = el.getAttribute('data-toggle-to');
            let toggles = document.querySelectorAll('[data-'+toggleTo+']');

            let obj = { watcher: el, type: type, elsToToggle: [] };

            if( toggles.length ){
                toggles.forEach( (e) => {
                    let valueToggle = e.getAttribute('data-'+toggleTo);
                    let target = { target: e, value: valueToggle };
                    obj.elsToToggle.push( target );
                });
            }

            toggleSets.push(obj);

        });
        //console.log(toggleSets);
        enableConditionals(toggleSets);
    }

	$('.tabs a').click(function(e){
		e.preventDefault();
		$('.tabs a').removeClass('active');
		$(this).addClass('active');
		let tag = $(this).attr('href');
		$('.tab-content').removeClass('active');
		$(tag).addClass('active');
	});

	$('.rules-list').on('click', '.delete-custom-field-rule', function(e){
		e.preventDefault();
		let id = $(this).attr('data-id');

		let formData = new FormData;
		formData.append('_method', 'delete');
		formData.append('id', id);

		HTTP.post('/admin/custom-fields/rule', formData)
		.then(response => {
			$('#ruleid-'+id).remove();
		})
		.catch(e => {
			console.log('Featured product error');
		});

	});

	$('.add-repeater-row-btn').click(function(e){
		e.preventDefault();
		let id = $(this).attr('data-id');

		let $rootHtml = $('#repeater-set'+id+' > .repeater-fields-container').clone();
		$($rootHtml).find('input').prop('disabled', false);
		$($rootHtml).find('textarea').prop('disabled', false);
		$($rootHtml).find('select').prop('disabled', false);
		$($rootHtml).find('radio').prop('disabled', false);
		$($rootHtml).find('checkbox').prop('disabled', false);

		let batchId = ID();
		let $inputs = $($rootHtml).find('input');
		let $textareas = $($rootHtml).find('textarea');
		let $selects = $($rootHtml).find('select');
		let $radios = $($rootHtml).find('radio');
		let $checkboxes = $($rootHtml).find('checkbox');

		$($rootHtml).find('.delete-repeater-row').attr('data-repeater-row-id', batchId);

		let lfmId = ID();
		let $lfm = $($rootHtml).find('[data-lfm]');
		let $lfmInput = $($rootHtml).find('[data-lfm-input]');
		let $lfmHolder = $($rootHtml).find('[data-lfm-holder]');

		$inputs.each(function(i){
			let newId = ID();
			let name = $($inputs[i]).attr('name');
			$($inputs[i]).attr('name', name+'['+batchId+']['+newId+']');
		});

		$textareas.each(function(i){
			let newId = ID();
			let name = $($textareas[i]).attr('name');
			$($textareas[i]).attr('name', name+'['+batchId+']['+newId+']');

			if( $($textareas[i]).attr('data-type') === 'editor' ){
				$($textareas[i]).addClass('editor');
			}
		});

		$selects.each(function(i){
			let newId = ID();
			let name = $($selects[i]).attr('name');
			$($selects[i]).attr('name', name+'['+batchId+']['+newId+']');
		});

		let radioId = ID();
		$radios.each(function(i){
			//let newId = ID();
			let name = $($radios[i]).attr('name');
			$($radios[i]).attr('name', name+'['+batchId+']['+radioId+']');
		});

		$checkboxes.each(function(i){
			let newId = ID();
			let name = $($checkboxes[i]).attr('name');
			$($checkboxes[i]).attr('name', name+'['+batchId+']['+newId+']');
		});

		$($lfm).attr('data-input', 'lfm-input'+lfmId);
		$($lfm).attr('data-preview', 'lfm-holder'+lfmId);
		$($lfm).attr('data-multiple', '0');
		$($lfmInput).attr('id', 'lfm-input'+lfmId);
		$($lfmHolder).attr('id', 'lfm-holder'+lfmId);

		$('#repeater-fields-group'+id).append($rootHtml);

		tinymce.remove('.cf-editor');
		setTimeout(function(){
			tinymce.init(cf_config);
		}, 1000);

	});

	let $toSlug = document.querySelector('.to-slug');
	let $slugInput = document.querySelector('.slug-input');
	if( $toSlug ){
		$toSlug.addEventListener('keyup', function(e){
			let toSlug = e.target.value;
			let slug = slugger(toSlug);
			$slugInput.value = slug;
		});
	}

	$('.delete-btn, .delete-object').click(function(e){
		//let conf = confirm('Are you sure you want to delete this?');
		//return conf;
		e.preventDefault();
		let $this = $(this);
		Swal.fire({
			  "title": 'Delete',
			  text: "Are you sure you want to delete this?",
			  type: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#333',
			  cancelButtonColor: '#ccc',
			  confirmButtonText: 'Yes, delete!'
		}).then(function (isConfirm) {
			if(isConfirm.dismiss !== 'cancel'){
				$this.parent().submit();
			}
		});
	});

    $('.delete-shipping-class').click(function(e){
        //let conf = confirm('Are you sure you want to delete this?');
        //return conf;
        e.preventDefault();
        let $this = $(this);
        Swal.fire({
              "title": 'Delete',
              text: "Are you sure you want to delete this?",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#333',
              cancelButtonColor: '#ccc',
              confirmButtonText: 'Yes, delete!'
        }).then(function (isConfirm) {
            if(isConfirm.dismiss !== 'cancel'){
                document.getElementById('shipping-classes-form').submit();
            }
        });
    });

	$('.delete-btn-taxonomy').click(function(e){
		//let conf = confirm('Are you sure you want to delete this?');
		//return conf;
		e.preventDefault();
		let $this = $(this);
		Swal.fire({
			  "title": 'Delete',
			  text: "Are you sure you want to delete this? Deleting this will orphan anything currently attached to it.",
			  type: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#333',
			  cancelButtonColor: '#ccc',
			  confirmButtonText: 'Yes, delete!'
		}).then(function (isConfirm) {
			if(isConfirm.dismiss !== 'cancel'){
				$this.parent().submit();
			}
		});
	});

    $('.gallery-images-list').on('click', '.remove-gallery-item-btn', function(e){
        e.preventDefault();
        let $this = $(this);
        //console.log(e);
        let id = e.target.getAttribute('data-id');
        //console.log(id);
        Swal.fire({
              "title": 'Remove',
              text: "Are you sure you want to remove this image from the gallery?.",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#333',
              cancelButtonColor: '#ccc',
              confirmButtonText: 'Yes,remove!'
        }).then(function (isConfirm) {
            if(isConfirm.dismiss !== 'cancel'){

                $('.gallery-item[data-id="'+id+'"]').remove();
                HTTP.post('/admin/galleries/images/'+id, { '_method': 'delete' });

            }
        });
    });

	$('.destroy-btn').click(function(e){
		//let conf = confirm('Are you sure you want to delete this?');
		//return conf;
		let url = $(this).attr('href');
		e.preventDefault();
		Swal.fire({
			  "title": 'Destroy',
			  text: "Are you sure you want to permently delete this?",
			  type: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#333',
			  cancelButtonColor: '#ccc',
			  confirmButtonText: 'Yes, destroy!'
		}).then(function (isConfirm) {
			if(isConfirm.dismiss !== 'cancel'){
				window.location.href = url;
			}
		});
	});

	$form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
	    e.preventDefault();
	    e.stopPropagation();
	  })
	  .on('dragover dragenter', function() {
	    $form.addClass('is-dragover');
	  })
	  .on('dragleave dragend drop', function() {
	    $form.removeClass('is-dragover');
	  })
	  .on('drop', function(e) {
	    droppedFiles = e.originalEvent.dataTransfer.files;
		//console.log('DROPPED');
		readURL( document.getElementById('media-file') );
		uploadProductFile();
	});

	$('.media-list').on('click', '.del-product-image', function(e){
		e.preventDefault();
		let mediaId = $(this).attr('data-media-id');
		let $this = $(this);

		HTTP.get('/site-admin/product-media/delete/'+mediaId)
		.then(response => {
	 	    $this.parent().remove();
	 	}).catch(response => {
	 	    //console.log(response);
	 	});
	});

	let $addVarBtn = document.querySelector('.add-variation-row');
	if( $addVarBtn ){
		$addVarBtn.addEventListener('click', function(e){
			e.preventDefault();
			addVarRow();
		});
	}

	$('.variation-rows').on('click', '.del-variation', function(e){
		e.preventDefault();
		delVarRow($(this));
	});

    $('#entry-status').change( (el) =>  {
        if ( el.target.value === 'P' ) {
            $('#publish-date-toggle').show();
        } else {
            $('#publish-date-toggle').hide();
        }
    });

    $('#create-note-btn').click( (el) =>  {
        el.preventDefault();
        $('.order-notes-message').html('');
        let orderNote = $('#order-note').val();
        let allow_public = $('#order-note-public').is(':checked')? 1 : 0;
        if( orderNote.length !== 0 ){
            let formData = new FormData;
            formData.append('note', orderNote);
            formData.append('allow_public', allow_public)

            HTTP.post('/admin/orders/'+orderId+'/note', formData)
            .then(response => {
                let html = '<li>';
                html += '<div class="order-note-date">By: '+response.data.note.user+' on '+response.data.note.created+' </div>';
                html += '<div class="order-note '+ ( response.data.note.public? 'public' : 'private' ) +'">';
                    html += response.data.note.notes;
                html += '</div>';
                html += '</li>';
                $('#order-note').val('');
                $('.order-notes-list ul').prepend(html);
                $('.no-notes-msg').remove();

                $('.order-notes-message').html('<p class="text-center" style="font-size: 14px">Note added.</p>');

            }).catch(response => {
                $('.order-notes-message').html('<p class="text-center" style="font-size: 14px">There was an error adding note.</p>');

            });
        }
    });

    $('.change-editor').click(function(e){
        e.preventDefault();

        let $changeEditors = $('.change-editor');
        let editorType = $(this).attr('data-editor-type');
        let $radios = $('input:radio[name=editor_type]');

        Swal.fire({
              "title": 'Change Content Editor',
              text: "Please save your changes before changing the content editor. Formatting issues could occur when changing editor types. When going from legacy editor to block editor, blocks cannot be resolved.",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#333',
              cancelButtonColor: '#ccc',
              confirmButtonText: 'Continue'
        }).then( (isConfirm) => {
            if(isConfirm.dismiss !== 'cancel'){

                $changeEditors.each(function(i,v){
                    $(v).removeClass('active');
                });
                $(this).addClass('active');
                $radios.each(function(i,v){
                    if( $(v).val() === editorType ){
                        $(v).attr('checked', true);
                    } else {
                        $(v).attr('checked', false);
                    }
                });
                let form = document.getElementById('object-form');
                form.submit();
            }
        });


    });

    $('.print-shipping-label-btn').click( (e) => {
        e.preventDefault();
        let id = e.target.getAttribute('data-order-id');
        HTTP.get('/admin/shipping-label/'+id)
        .then(response => {
            if( response.data.success ){
                $('.print-shipping-label-btn').hide();
                let html = '<strong>Tracking #:</strong> <a href="'+response.data.tracking_url+'" target="_blank">'+response.data.tracking_number+'</a><br>';
                html += '<a href="'+response.data.label_url+'" class="view-shipping-label-link" target="_blank"><i class="fal fa-barcode-read"></i>View Shipping Label</a>';
                $('.tracking-label-info').html(html);
            }
        })
        .catch(e => {
            console.log('Shipping label error.');
        });
    });

    $('.resend-order-receipt').click( el => {
        el.preventDefault();
        HTTP.get('/admin/resend-reciept/'+orderId)
        .then(response => {
            $('.resend-order-receipt-wrap').html('<span>Email sent!</span>');
        })
        .catch(e => {

        });
    });

    $('.view-payment-details-btn').click( (e) => {
        e.preventDefault();
    });

    $('.view-payment-details-btn').click( (e) => {
        e.preventDefault();
        let id = e.target.getAttribute('data-transaction-id');
        HTTP.get('/admin/transaction-details/'+id)
        .then(response => {
            if( response.data.success ){
                let res = response.data;
                let html = '<p><strong>Status:</strong> '+res.status+'<br>';
                html += '<strong>Initial Charge:</strong> $'+res.amount+'</p>';
                html += '<h4>Billed To</h4>';
                if( res.billing_details.name !== null ){
                    html += '<p>'+res.billing_details.name+'<br>';
                }
                if( res.billing_details.email !== null ){
                    html += res.billing_details.email+'<br>';
                }
                if( res.billing_details.phone !== null ){
                    html += '<p>'+res.billing_details.phone+'<br>';
                }
                if( res.billing_details.address.street1 !== null ){
                    html += res.billing_details.address.street1+'<br>';
                }
                if(res.billing_details.address.street2 !== null){
                    html += res.billing_details.address.street2+'<br>';
                }
                if( res.billing_details.address.city !== null ){
                    html += res.billing_details.address.city+' '+res.billing_details.address.state+', ';
                }
                if( res.billing_details.address.zip !== null ){
                    html += res.billing_details.address.zip+'<br>';
                }
                if( res.billing_details.address.country !== null ){
                    html += res.billing_details.address.country;
                }
                html += '</p>';

                html += '<p><strong>Type:</strong> '+res.payment_details.type+'<br>';
                html += '<strong>Method:</strong> '+res.payment_details.method+'<br>';
                html += '<strong>Last 4:</strong> '+res.payment_details.last_four+'</p>';

                $('.payment-details').html(html);
            }
        })
        .catch(e => {
            console.log('Shipping label error.');
        });
    });

    $('#order-status').change( (el) => {
        if( $('#order-status').val() === '86' ){
            $('.refund-order-row').show();
            $('.refund-notes').show();
        } else {
            $('.refund-order-row').hide();
            $('.refund-notes').hide();
        }

        if( $('#order-status').val() === '4' || $('#order-status').val() === '86' ){
            $('.refund-notes').show();
        } else {
            $('.refund-notes').hide();
        }


    });

    $('.refund-item-btn').click( (el) => {
        e.preventDefault();
        let target = el.target;
        let amount = target.getAttribute('data-amount');
        let title = target.getAttribute('data-item-title');
        let lineId = target.getAttribute('data-line-seq');
        let refId = target.getAttribute('data-order-ref');
        let qty = parseInt(target.getAttribute('data-qty'));
        let lineItem = parseInt(target.getAttribute('data-line-id'));

        refundObj = {
                line_id: lineId,
                ref_id: refId,
                qty: qty,
                line_item: lineItem,
                full_amount: 1,
                notes: ''
        };

        $('#line-refund-amount').val(amount);
        $('#line-refund-qty').val(qty);
        $('.item-refund-title').html(title);

        $('.line-refund-modal').fadeIn('fast');

    });

    $('.close-refund-item-modal').click((el) => {
        el.preventDefault();
        refundObj = {
                amount: 0.00,
                line_id: 0,
                ref_id: '',
                qty: 0,
                line_item: '',
                full_amount: 1
        };
        $('.refund-messages').html('');
        $('.item-refund-title').html('');
        $('.line-refund-modal').fadeOut('fast');
        $('#line-shipping-amount').val('');
    })

    $('.submit-refund-item-btn').click( (el) => {
        e.preventDefault();
        $('.refund-messages').html('');

        let qty = parseInt($('#line-refund-qty').val());
        let notes = $('#line-refund-notes').val();

        if( qty > refundObj.qty ){
            $('.refund-messages').html('QTY cannot exceed the QTY purchased.');
            return false;
        }

        if( qty === 0 ){
            $('.refund-messages').html('QTY must be greater than 0.');
            return false;
        }

        if( notes === '' ){
            $('.refund-messages').html('Enter your refund notes.');
            return false;
        }

        var shippingAmount = parseFloat($('#line-shipping-amount').val());

        refundObj.qty = qty;
        refundObj.notes = notes;
        refundObj.shipping_amount = shippingAmount;

        HTTP.post('/admin/order-lines/refund/'+refundObj.line_id, refundObj)
        .then(response => {
            if( response.data.success ){
                refundObj = {
                    amount: 0.00,
                    shipping_amount: 0.00,
                    line_id: 0,
                    ref_id: '',
                    qty: 0,
                    line_item: '',
                    full_amount: 1,
                    notes: ''
                };
                $('.line-refund-modal').fadeOut('fast');
                $('.item-refund-title').html('');
                $('#line-shipping-amount').val('');

                location.reload();

            }
        })
        .catch(e => {
            $('.refund-messages').html(e.message);
            if (e.response) {
                console.log(e.response.data);
                $('.refund-messages').append('<br>'+e.response.data.message);
            }

        })

    });

    const datetimePicker = flatpickr('.datetime-picker', { enableTime: true, dateFormat: 'Y-m-d h:i K' });

    let $datePickerInput = document.querySelectorAll('.start-date');
    let $datePickerProfit = document.querySelectorAll('.profit-start-date');
    if( $datePickerInput.length ){
        const startDate = datepicker('.start-date', { id: 1 });
        const endDate = datepicker('.end-date', { id: 1 });

        startDate.getRange();
        endDate.getRange();
    }

    if( $datePickerProfit.length ){
        const profitStartDate = datepicker('.profit-start-date', { id: 2 });
        const profitEndDate = datepicker('.profit-end-date', { id: 2 });

        profitStartDate.getRange();
        profitEndDate.getRange();
    }

    if($clearDatetimePicker.length){
        $clearDatetimePicker.forEach( (el) => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                let pickerId = e.target.getAttribute('data-date-picker');
                let dPicker = document.querySelector('[data-date-picker="'+pickerId+'"]');
                dPicker.value = '';
            });
        });
    }

});

import draggable from 'vuedraggable';
import Editor from '@tinymce/tinymce-vue';
import quickbars from 'tinymce/plugins/quickbars/plugin.js';
import anchor from 'tinymce/plugins/anchor/plugin.js';
import link from 'tinymce/plugins/link/plugin.js';
import lists from 'tinymce/plugins/lists/plugin.js';
import placeholder from './plugins/placeholder/plugin.js';
import { mixin as clickaway } from 'vue-clickaway';


let BlockEditor = document.querySelector('#block-editor');

let tags = {
            p: 'p',
            h1: 'h1',
            h2: 'h2',
            h3: 'h3',
            h4: 'h4',
            h5: 'h5',
            div: 'div',
            section: 'section',
            blockquote: 'blockquote',
            span: 'span',
            ul: 'ul',
            ol: 'ol',
        };

if( BlockEditor ){

if( typeof blocks !== 'undefined' ){
    blocks.forEach(function(v){

        let props = ['block', 'blockIndex'];
            Vue2.component( v.name, {
                props: props,
                template: v.template,
                components: {
                    'tinymce-editor': Editor,
                    draggable
                },
                data: () => ({
                    bIndex: 0,
                    groupIndex: 0,
                    fieldIndex: 0,
                    valueIndex: 0,
                    tag: tags
                }),
                computed: {

                },
                methods: {
                    chooseBlockImage( blockIndex, groupIndex, valueIndex ){
                        this.bIndex = blockIndex;
                        this.groupIndex = groupIndex;
                        this.valueIndex = valueIndex;
                        fm.fileType = 'image';
                        fm.standaloneMode = false;
                        fm.selectionMode = true;
                        fm.callback = true;
                        fm.isBlockEditor = true;
                        fm.backgroundImage = true;
                        fm.multiple = false;
                        fm.boot();
                        fm.showFileManager();

                        window.addEventListener("message", this.setBlockImage, false);
                    },
                    setBlockImage(e){
                        if( e.data.hasOwnProperty('blockAction') && e.data.blockAction ){
                            this.$root.currentBlocks[this.bIndex].field_groups[this.groupIndex].fields[this.valueIndex].value = e.data.content;
                            window.removeEventListener('message', this.setBlockImage, false);
                        }
                    },
                    getOption(blockItem, optionName){
                        let value = '';
                        blockItem.options.forEach( (v) => {
                            if( v.name === optionName ){
                                value = v.value;
                            }
                        });
                        return value;
                    }
                }
            });

    });

}

Vue2.component('block-options', {
    props: [ 'block', 'blockIndex' ],
    mixins: [ clickaway ],
    data: () => ({

    }),
    methods: {
        away(){
            this.block.showBlockItemOptions = false;
        }
    },
    mounted(){
       //console.log(this.block.options);
    },
    template: `<div>
                <div class="block-options">
                <div class="inner">
                    <a href="#" class="block-item-options-toggle" @click.prevent="block.showBlockItemOptions = !block.showBlockItemOptions" role="button">
                        {{ block.title }} Options
                    </a>
                </div>
            </div>
            <div v-if="block.showBlockItemOptions" v-on-clickaway="away" class="block-item-options-list" v-cloak>
                        <div v-for="option in block.options">
                            <div v-if="option.type === 'text' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="block-item-option-value-col">
                                        <input type="text" v-model="option.value">
                                    </div>
                                </label>
                            </div>
                            <div v-if="option.type === 'dropdown' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="select-wrapper">
                                        <select v-model="option.value">
                                            <option value="">Choose ...</option>
                                            <option v-for="opt in option.options" :value="opt.value">{{ opt.label }}</option>
                                        </select>
                                    </div>
                                </label>
                            </div>
                            <div v-if="option.type === 'checkbox' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <input type="checkbox" v-model="option.value" value="1" >
                                </label>
                            </div>
                            <div v-if="option.type === 'color' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="block-item-option-value-col">
                                        <input type="color" v-model="option.value">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>`
});

Vue2.component('block-item-actions', {
    props: [ 'block', 'blockItem', 'blockIndex' ],
    mixins: [ clickaway ],
    data: () => ({

    }),
    methods: {
        removeBlockItem(){
            this.$root.currentBlocks[this.blockIndex].field_groups.splice( this.$root.currentBlocks[this.blockIndex].field_groups.indexOf(this.blockItem), 1);

            if( this.$root.currentBlocks[this.blockIndex].field_groups.length === 0 ){
                this.$root.currentBlocks.splice( this.$root.currentBlocks[this.blockIndex], 1);
            }

        },
        away(){
            this.blockItem.showBlockItemOptions = false;
        }
    },
    mounted(){
       //console.log(this.blockItem.options);
    },
    template: `<div>
                <div class="block-item-options">
                <div class="inner">
                    <span v-if="$root.currentBlocks[blockIndex].field_groups.length > 1" class="block-item-drag-handle"><i class="fas fa-grip-horizontal"></i></span>
                    <a href="#" class="block-item-options-toggle" @click.prevent="blockItem.showBlockItemOptions = !blockItem.showBlockItemOptions" role="button">
                        <i class="fal fa-ellipsis-h-alt"></i>
                    </a>
                </div>
            </div>
            <div v-if="blockItem.showBlockItemOptions" v-on-clickaway="away" class="block-item-options-list" v-cloak>
                        <div v-for="option in blockItem.options">
                            <div v-if="option.type === 'text' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="block-item-option-value-col">
                                        <input type="text" v-model="option.value">
                                    </div>
                                </label>
                            </div>
                            <div v-if="option.type === 'dropdown' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="select-wrapper">
                                        <select v-model="option.value">
                                            <option value="">Choose ...</option>
                                            <option v-for="opt in option.options" :value="opt.value">{{ opt.label }}</option>
                                        </select>
                                    </div>
                                </label>
                            </div>
                            <div v-if="option.type === 'checkbox' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <input type="checkbox" v-model="option.value" value="1" >
                                </label>
                            </div>
                            <div v-if="option.type === 'color' " class="bi-option-item">
                                <label><span>{{ option.label }}</span>
                                    <div class="block-item-option-value-col">
                                        <input type="color" v-model="option.value">
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div v-if="block && block.group || blockItem.group" class="bi-delete-item">
                            <a href="#" class="delete-block-item" @click.prevent="removeBlockItem(blockItem)">Delete Block Item</a>
                        </div>
                    </div>
                </div>`
});

Vue2.component('child-block-chooser', {
    props: [ 'field' ],
    mixins: [ clickaway ],
    data: () => ({
        showBlockPicker: false,
        blocks: blocks,
    }),
    methods: {
        addChildBlock(newBlock){
            //console.log('Add child block');
            this.showBlockPicker = false;
            if( typeof this.field.blocks === 'undefined' ){
                this.field['blocks'] = [];
            }
            this.field.blocks.push(newBlock);

        },
    },
    mounted(){
        //console.log('mounted child block chooser');
    },
    template: `<div>
                <a href="#" class="child-block-chooser-toggle" @click.prevent="showBlockPicker = !showBlockPicker" role="button"><i class="fal fa-plus"></i></a>
                <div class="child-block-chooser" v-if="showBlockPicker" v-cloak>
                    <a href="#" class="block-append-btn" v-for="newblock in blocks" @click.prevent="addChildBlock(newblock)" :title="newblock.title" role="button">
                        <span>{{ newblock.title }}</span> <i v-if="newblock.icon" :class="'fal fa-'+newblock.icon+' fa-fw'"></i>
                    </a>
                </div>
            </div>`
});

Vue2.component('child-blocks', {
    props: [ 'field' ],
    mixins: [ clickaway ],
    data: () => ({
        loading: false
    }),
    components: {
        draggable,
        'tinymce-editor': Editor
    },
    methods: {
        deleteBlock(block){
            this.field.blocks.splice( this.field.blocks.indexOf(block) , 1);
        }
    },
    mounted(){
        //console.log('mounted child blocks', this.field);
    },
    template: `<div class="child-blocks">
                <div v-for="(block, blockIndex) in field.blocks" class="child-block">
                    <tinymce-editor v-if="block.contentEditable && !block.template && block.tag" v-model="block.value" :init="$root.tinyInitInlineParagraph" :tag-name="block.tag"></tinymce-editor>
                    <tinymce-editor v-if="block.contentEditable && !block.template && !block.tag" v-model="block.value" :init="$root.tinyInitInlineFreeText"></tinymce-editor>
                    <keep-alive>
                        <component v-if="!block.contentEditable" :block="block" :block-index="blockIndex" v-bind:is="block.name" />
                    </keep-alive>
                    <a class="remove-child-block" href="#" @click.prevent="deleteBlock(block)" >&times;</a>
                </div>
            </div>`
});

window.blockEditor = new Vue2({
    el: '#block-editor',
    mixins: [ clickaway ],
    data: () => ({
        errors: [],
        saving: false,
        loading: false,
        active: true,
        blocks: [],
        showBlockPicker: false,
        currentBlocks: [],
        tag: tags,
        tinyInitInlineParagraph: {
            skin: "oxide-dark",
            menubar: false,
            toolbar: false,
            placeholder: 'Start typing ...',
            plugins: [ 'quickbars', 'anchor', 'link', 'placeholder', 'paste' ],
            quickbars_insert_toolbar: false,
            hidden_input: false,
            inline: true,
            paste_as_text: true,
            quickbars_selection_toolbar: 'bold italic underline quicklink anchor forecolor'
        },
        tinyInitInlineFreeText: {
            skin: "oxide-dark",
            menubar: false,
            toolbar: 'styleselect fontsizeselect | forecolor backcolor | bullist numlist | link | bold italic alignleft aligncenter alignright alignjustify | image | table tabledelete | hr | blockquote | anchor | pastetext',
            plugins: [ 'anchor', 'link', 'table', 'lists', 'hr', 'image', 'imagetools', 'paste' ],
            fontsize_formats: "8px 10px 12px 14pt 16px 18px 20px 22px 24px 26px 28px 30px 36px",
            hidden_input: false,
            style_formats: editorStyles,
            inline: false,
            contextmenu: "link imagetools styleselect table",
            branding: false,
            paste_as_text: true,
            body_id: 'block-editor-tiny',
            content_css : editorCss,
            content_css_cors: true,
            style_formats_merge: true,
            file_picker_callback: function(callback, value, meta) {
                //console.log(meta, value);
                var type = 'image' === meta.filetype ? 'image' : 'file';
                showFMeditor(type);
                window.addEventListener('message', (event) => {
                    if( event.data.mceAction === 'insert' ){
                        let name = event.data.content.split('/').pop();
                        let obj = type === 'image'? { alt: '' } : { text: name } ;
                        callback(event.data.content, obj);
                    }
                });
            },
            quickbars_selection_toolbar: 'bold italic underline bullist numlist | alignleft aligncenter alignright | image quicklink anchor forecolor'
        },
        tinyInitInlineHeading: {
            skin: "oxide-dark",
            menubar: false,
            toolbar: false,
            plugins: [ 'quickbars', 'anchor', 'link', 'paste' ],
            quickbars_insert_toolbar: false,
            inline: true,
            paste_as_text: true,
            quickbars_selection_toolbar: 'bold italic underline quicklink anchor forecolor'
        },
        tinyInitInlineBlockquote: {
            skin: "oxide-dark",
            menubar: false,
            toolbar: false,
            plugins: [ 'quickbars', 'placeholder', 'paste' ],
            placeholder: 'Quote ...',
            quickbars_insert_toolbar: false,
            inline: true,
            paste_as_text: true,
            quickbars_selection_toolbar: 'bold italic underline'
        }
    }),
    components: {
        draggable,
        'tinymce-editor': Editor
    },
    computed:{
        blockContent(){
            return JSON.stringify(this.currentBlocks);
        }
    },
    methods: {
        dragStart(){
            console.log('drag.start');
            /*console.log(this.$root.$refs.editor);
            this.$root.$refs.editor.forEach( (obj) => {
                console.log('de-activating', obj);
                console.log(obj.editor.id);
                console.log(obj.elementId);
                obj.editor.editorManager.EditorManager.execCommand('mceRemoveEditor', false, obj.elementId);
            });*/
        },
        dragEnd(){
            console.log('drag.end');
            /*setTimeout( () => {
                this.$root.$refs.editor.forEach( (obj) => {
                    console.log('de-activating', obj);
                    //obj.editor.editorManager.EditorManager.execCommand('mceRemoveEditor', false, obj.elementId);
                    console.log('re-activating', obj);
                    console.log(obj.editor.id);
                    console.log(obj.elementId);
                    let id = ( obj.editor.id === obj.elementId )? obj.editor.id : obj.elementId;
                    obj.editor.editorManager.EditorManager.execCommand('mceAddEditor', false, id);
                });
            }, 100 );*/

        },
        dragUpdate(){
            console.log('drag.updated');
        },
        openImageMediaDialog(){
            showMediaDialog('image');
        },
        useMediaImage(){
            let imageUrl = document.getElementById('media-image-dialog-url').value;
            let imageAltText = document.getElementById('media-image-dialog-alt-text').value;
        },
        appendBlock(block, index){
            this.showBlockPicker = false;
            let id = this.synthId();
            let newBlock = {};
            newBlock['id'] = id;
            newBlock['tag'] = block.tag? block.tag : false;
            newBlock['template'] = block.template? block.template : false;
            newBlock['contentEditable'] = block.contentEditable? block.contentEditable : false;
            newBlock['showBlockItemOptions'] = false;
            newBlock['value'] = '';
            newBlock['title'] = block.title;
            newBlock['name'] = block.name;
            newBlock['group'] = block.group? block.group : false;
            newBlock['icon'] = block.icon? block.icon : false;
            newBlock['field_groups'] = [];
            newBlock['options'] = block.options? block.options : [];

            let fields = block.fields ? JSON.parse(JSON.stringify(block.fields)) : [];

            fields.forEach( (v) => {
                if( typeof v.allow_blocks === 'undefined' ){
                    v.allow_blocks = false;
                }
                if( typeof v.blocks === 'undefined' ){
                    v.blocks = [];
                }
            });

            let fieldGroup = {
                fields: fields,
                showBlockItemOptions : false,
                options: typeof block.group_options !== 'undefined' ? block.group_options : []
            };

            newBlock.field_groups.push( fieldGroup ) ;

            this.currentBlocks.splice(index+1, 0, newBlock );

        },
        removeBlock(block){
            let i = this.currentBlocks.indexOf(block);
            this.currentBlocks.splice(i,1);
        },
        addBlockGroup(block){
            let modelGroup = [];
            let copiedBlock = JSON.parse(JSON.stringify(block));
            let group = copiedBlock.field_groups[0];

            group['showBlockItemOptions'] = false;

            group.options.forEach( (v) => {
                v.value = '';
            });
            group.fields.forEach( (v) => {
                v.value = '';
                v.blocks = [];
            });
            block.field_groups.push(group);
        },
        closeBlockChooser(){
            this.showBlockPicker = false;
        },
        synthId(){
            return Math.random().toString(36).substr(2, 9);
        },
    },
    mounted(){
        this.blocks = typeof blocks !== 'undefined' ? blocks : [];
        this.currentBlocks = typeof currentBlocks !== 'undefined' ? currentBlocks : [];
    }
});

}
