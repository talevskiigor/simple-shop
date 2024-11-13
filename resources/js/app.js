import './bootstrap';

// import 'tinymce/tinymce';
// import tinymce from 'tinymce/tinymce';
// import 'tinymce/plugins';

// import 'tinymce/skins/ui/oxide/skin.min.css';
// import 'tinymce/skins/content/default/content.min.css';
// import 'tinymce/skins/content/default/content.css';
// import 'tinymce/icons/default/icons';
// import 'tinymce/themes/silver/theme';
// import 'tinymce/models/dom/model';


// .. After imports init TinyMCE ..
// window.addEventListener('DOMContentLoaded', () => {
//     tinymce.init({
//         selector: 'textarea',
//
//         /* TinyMCE's configuration options */
//         skin: false,
//         content_css: false,
//         /* Extra options */
//         plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable export'
//     });
// });

$(function () {
    $("[data-bs-toggle='tooltip']").tooltip();
    $("[rel='tooltip']").tooltip();
});
