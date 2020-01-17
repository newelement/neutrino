window.Vue = require('vue');
import axios from 'axios';
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

const HTTP = axios.create(axios.defaults.headers.common = {
	'X-Requested-With': 'XMLHttpRequest',
	'X-CSRF-TOKEN' : window.app.csrfToken,
	'Content-Type': 'multipart/form-data'
});

window.fm = new Vue({
    el: '#filemanager',
	data: () => ({
        errors: [],
        saving: 0,
        loading: false,
		savingEdit: false,
        showOverlay: false,
		paths: [{ name: 'uploads', path: 'uploads' }],
		path: 'uploads',
		standaloneMode: true,
        isBlockEditor: false,
        backgroundImage: false,
        inlineImage: false,
        imageId: '',
		selectionMode: true,
		multiple: true,
		fileType: 'all',
		callback: false,
		inputId: '',
		previewId: '',
		sizeOptions: [],
		showSizeChooser: false,
		folderModal: false,
		editImageModal: false,
		uploadModal: false,
		showFileInfoPanel: false,
		folderName: '',
		selectedFile: {},
		items: { files : [] },
		uploadPercent: 0,
		baseUrl: '',
		dropzoneOptions: {
			url: '/admin/filemanager/upload',
			maxFilesize: 10,
			headers: {
				'X-CSRF-TOKEN' : window.app.csrfToken
			},
			paramName: function(n) {
				return "file[]";
			},
			dictDefaultMessage: "Upload Files Here",
			includeStyling: false,
			previewsContainer: false,
			thumbnailWidth: 250,
			thumbnailHeight: 140,
			uploadMultiple: true,
			parallelUploads: 20
		},
		imgSrc: '',
		cropImg: '',
		editImageData: null,
		currentEditImage: ''
    }),
	components: {
		vueDropzone: vue2Dropzone,
		VueCropper
	},
    filters: {
		itemName(value){
			let folders = value.split('/');
			return folders[folders.length-1];
		},
	},
    created() {
    },
	computed: {
		selectedFiles(){
			return this.items.files.filter( res => {
				return res.selected;
			});
		}
	},
    methods: {
		boot(){
			this.items = { folders: [], files : [] };
			HTTP.get('/admin/filemanager?file_type='+this.fileType)
			.then(response => {
				this.items = response.data.fileData.items;
			})
			.catch(e => {
				this.log(e);
			});
		},
		gotoPath(path){
			this.path = path;
			this.generatePathLinks(path);
			this.closeFileInfoPanel();
			HTTP.get('/admin/filemanager?path='+path+'&file_type='+this.fileType)
			.then(response => {
				this.items = response.data.fileData.items;
			})
			.catch(e => {
				this.log(e);
			});
		},
		generatePathLinks(path){
			let paths = path.split('/');
			let pathArr = [];
			let val = '';
			let slash = '';
			paths.forEach(function(v, i){
				if( i > 0 ){
					slash = '/';
				}
				val += slash+v;
				let item = { name: v, path: val };
				pathArr.push(item);
			});
			this.paths = pathArr;
		},
		showCreateFolderModal(){
			this.folderModal = true;
		},
		showUploadModal(){

		},
		closeUploadFolderModal(){
			this.folderModal = false;
			this.folderName = '';
		},
		closeEditImageModal(){
			this.editImageModal = false;
			this.showOverlay = false;
		},
		createFolder(){
			if( this.folderName.length > 2 ){
				let formData = new FormData;
				formData.append('folder_name', this.folderName);

				HTTP.post('/admin/filemanager/folder?path='+this.path, formData)
				.then(response => {
					this.items.folders.unshift(this.path+'/'+this.folderName);
					this.folderName = '';
					this.folderModal = false;
				})
				.catch(e => {
					this.log(e);
				});
			}
		},
		deleteFolder(folder){
			let formData = new FormData;
			formData.append('folder_name', folder);
			formData.append('_method', 'delete');

			HTTP.post('/admin/filemanager/folder?path='+this.path, formData)
			.then(response => {
				this.items.folders.splice( this.items.folders.indexOf(folder), 1);
			})
			.catch(e => {
				this.log(e);
			});
		},
		getUploadParams(){
			return { path: this.path };
		},
		dzUploadProgress(f, percent){
			this.uploadPercent = percent;
		},
		dzSendingFiles(){
		},
		dzSending(file, xhr, formData){
			formData.append("path", this.path);
		},
		dzFileAdded(){

		},
		dzSuccess(f, data){
			let self = this;
			data.files.forEach(function(v){
				self.items.files.push(v);
			});
			this.uploadPercent = 0;
		},
        dzError(f, data){
            this.snackbar('error', data.message);
            this.uploadPercent = 0;
        },
		deleteFile(file){
			let formData = new FormData;
			formData.append('path', file.path);
			formData.append('_method', 'delete');

			HTTP.post('/admin/filemanager/file', formData)
			.then(response => {
				this.items.files.splice( this.items.files.indexOf(file), 1);
			})
			.catch(e => {
				this.log(e);
			});
		},
		showEditImage(file, i){

			var canvas = document.createElement('canvas');
			var context = canvas.getContext('2d');
			var img = document.getElementById('image-'+i);
			canvas.width = img.width;
			canvas.height = img.height;
			context.drawImage(img, 0, 0 );
			var myData = context.getImageData(0, 0, img.width, img.height);

			let imgEl = document.getElementById('image-'+i);
			this.imgSrc = imgEl.getAttribute('src');
			this.editImageModal = true;
			this.showOverlay = true;

			this.currentEditImage = file;
		},
		selectFile(file){

			if(this.callback){
				if( file.image ){
					this.chooseSize(file);
				} else {
					this.useFile('/storage/'+file.path);
				}
			}

            if( this.selectionMode && !this.callback ){
                file.selected = !file.selected;
                if( !this.multiple && this.selectedFiles.length > 1 ){
                    file.selected = !file.selected;
                    this.showError('You can only select one file.');
                }
            }

			if( !this.standaloneMode && this.selectionMode && this.inputId.length > 0){

				let input = document.getElementById(this.inputId);
				let preview = document.getElementById(this.previewId);

				if( !this.multiple ){
					input.value = '/storage/'+file.path;
					if( this.fileType === 'image' ){
						preview.innerHTML = '';
						let  $img = document.createElement('img');
						$img.setAttribute('class', 'lfm-preview-image');
						$img.setAttribute('src', '/storage/'+file.path);
				        preview.appendChild($img);
						preview.innerHTML = preview.innerHTML+'<a class="clear-lfm-image" data-preview-id="'+this.previewId+'" data-input-id="'+this.inputId+'" href="/">&times;</a>';
					}

                    this.closeFileManager();

				} else {
                    let id = this.getId();
					let  $input = document.createElement('input');
                    $input.setAttribute('type', 'hidden');
                    $input.setAttribute('name', 'gallery_images[]');
                    $input.setAttribute('data-gallery-image-id', id);
                    $input.value = '/storage/'+file.path;
					input.appendChild($input);
					if( this.fileType === 'image' ){
                        let $galImgWrap = document.createElement('div');
                        $galImgWrap.setAttribute('class', 'gal-img-item');
                        $galImgWrap.setAttribute('data-gallery-image-id', id);

                        let $aClose = document.createElement('a');
                        $aClose.setAttribute('data-gallery-image-id', id);
                        $aClose.setAttribute('class', 'clear-lfm-gallery-image');
                        $aClose.setAttribute('href', '#');
                        let times = document.createTextNode('\u00D7');
                        $aClose.appendChild(times);

						let  $img = document.createElement('img');
						$img.setAttribute('class', 'lfm-preview-image');
                        $img.setAttribute('data-gallery-image-id', id);
						$img.setAttribute('src', '/storage/'+file.path);
                        $galImgWrap.appendChild($img);
                        $galImgWrap.appendChild($aClose);
						preview.appendChild($galImgWrap);
                    }
				}
			}

			if( this.standaloneMode ){
				this.baseUrl = window.location.protocol+'//'+window.location.hostname;
				this.selectedFile = file;
				this.openFileInfoPanel();
			}
		},
        getId(){
            return '_' + Math.random().toString(36).substr(2, 9);
        },
		openFileInfoPanel(){
			this.showFileInfoPanel = true;
		},
		closeFileInfoPanel(){
			this.selectedFile = {};
			this.showFileInfoPanel = false;
		},
		chooseSize(file){
			console.log(file);
			this.sizeOptions = file.sizes;
			this.showSizeChooser = true;
		},
		closeSizeChooser(){
			this.showSizeChooser = false;
			this.sizeOptions = [];
		},
		crop(){
			//this.$refs.cropper.crop();
		},
		cropImage() {
	      this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
	    },
	    flipX() {
	      const dom = this.$refs.flipX;
	      let scale = dom.getAttribute('data-scale');
	      scale = scale ? -scale : -1;
	      this.$refs.cropper.scaleX(scale);
	      dom.setAttribute('data-scale', scale);
	    },
	    flipY() {
	      const dom = this.$refs.flipY;
	      let scale = dom.getAttribute('data-scale');
	      scale = scale ? -scale : -1;
	      this.$refs.cropper.scaleY(scale);
	      dom.setAttribute('data-scale', scale);
	    },
	    getCropBoxData() {
	      this.editImageData = JSON.stringify(this.$refs.cropper.getCropBoxData(), null, 4);
	    },
	    getData() {
	      this.editImageData = JSON.stringify(this.$refs.cropper.getData(), null, 4);
		  console.log(this.editImageData);
	    },
	    reset() {
	      this.$refs.cropper.reset();
	    },
	    rotate(deg) {
	      this.$refs.cropper.rotate(deg);
	    },
	    setCropBoxData() {
	      if (!this.editImageData) return;
	      this.$refs.cropper.setCropBoxData(JSON.parse(this.editImageData));
	    },
	    setData() {
	      if (!this.editImageData) return;
	      this.$refs.cropper.setData(JSON.parse(this.editImageData));
	    },
	    zoom(percent) {
	      this.$refs.cropper.relativeZoom(percent);
	    },
		savedEditedImage(){
			let image = this.$refs.cropper.getCroppedCanvas().toDataURL();
			let formData = new FormData();
			formData.append('image', image);
			formData.append('current_image', this.currentEditImage.info.basename);
			formData.append('path', this.path);
			this.savingEdit = true;
			HTTP.post('/admin/filemanager/edit-image', formData)
			.then(response => {
				this.savingEdit = false;
				if( response.data.success ){
					let i = this.items.files.indexOf(this.currentEditImage);
					this.items.files[i] = response.data.file;
					this.editImageModal = false;
					this.showOverlay = false;
				} else {
                   this.snackbar('error', response.data.message);
                }
			})
			.catch(e => {
				this.log(e);
			});

		},
        handleFileUpload(e){

		},
		useFile(url){
            if( this.isBlockEditor ){
                if( this.backgroundImage ){
                    window.postMessage({
                        blockAction: true,
                        content: url
                    });
                } else if (this.inlineImage) {

                } else {

                }

            } else {
    			window.postMessage({
    		      	mceAction: 'insert',
    		      	content: url
    		  	});
            }
			this.showSizeChooser = false;
			this.sizeOptions = [];
			this.closeFileManager();
		},
        useChosenFiles(){
            this.closeFileManager();
        },
		showFileManager(){
			let $fm = document.getElementById('filemanager');
			$fm.classList.add('show');
		},
		closeFileManager(){
			this.closeFileInfoPanel();
			let $fm = document.getElementById('filemanager');
			$fm.classList.remove('show');
		},
        snackbar(type, message){
            var x = document.getElementById("snackbar");
            x.innerHTML = message;
              x.className = "show "+type;
              setTimeout(function(){ x.className = x.className.replace("show "+type, ""); }, 4000);
        },
		log(e){
			console.log(e);
		},
		showError(message){
			let self = this;
			this.errors.push({ message: message });
			setTimeout(function(){
				self.errors = [];
			}, 4000);
		}
	},
	beforeMount(){
    },
    mounted: function(){
		//this.boot();
    }
});
