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
        fmUploading: false,
		savingEdit: false,
        showOverlay: false,
		paths: [{ name: 'uploads', path: 'uploads' }],
		path: 'uploads',
		standaloneMode: true,
        isBlockEditor: false,
        isGallery: false,
        galleryItems: [],
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
		gotoPath(item){
            item.loading = true;
			this.path = item.path;
			this.generatePathLinks(item.path);
			this.closeFileInfoPanel();
			HTTP.get('/admin/filemanager?path='+item.path+'&file_type='+this.fileType)
			.then(response => {
                this.galleryItems = [];
				this.items = response.data.fileData.items;
                item.loading = false;
			})
			.catch(e => {
				this.log(e);
                item.loading = false;
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
				let item = { name: v, path: val, loading: false };
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
					this.items.folders.unshift( {path: this.path+'/'+this.folderName, loading: false} );
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
			formData.append('folder_name', folder.path);
			formData.append('_method', 'delete');
            folder.loading = true;
			HTTP.post('/admin/filemanager/folder?path='+this.path, formData)
			.then(response => {
                folder.loading = false;
				this.items.folders.splice( this.items.folders.indexOf(folder), 1);
			})
			.catch(e => {
				this.log(e);
                folder.loading = false;
			});
		},
		getUploadParams(){
			return { path: this.path };
		},
		dzUploadProgress(f, percent){
			this.uploadPercent = percent;
		},
        dzSending(){
            this.fmUploading = true;
        },
		dzSendingFiles(){
            this.fmUploading = true;
		},
        dzQueueComplete(){

        },
        dzComplete(){
            this.fmUploading = false;
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
			formData.append('path', file.path+'/'+file.filename);
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
            let self = this;
			let canvas = document.createElement('canvas');
			let context = canvas.getContext('2d');
			let emptyImg = document.getElementById('image-'+i);
            let originalUrl = emptyImg.getAttribute('data-original-image');
            emptyImg.src = originalUrl;

            var img = new Image();
            img.addEventListener("load", (el) => {

                canvas.width = el.path[0].width;
                canvas.height = el.path[0].height;
                context.drawImage(img, 0, 0 );
                var myData = context.getImageData(0, 0, el.path[0].width, el.path[0].height);

                self.imgSrc = originalUrl;
                self.editImageModal = true;
                self.showOverlay = true;

                self.currentEditImage = file;

            });
            img.src = originalUrl;

		},
        selectAllImages(){
            this.items.files.forEach( (obj) => {
                if( obj.image ){
                    this.selectFile(obj);
                }
            });
        },
		selectFile(file){

			if(this.callback){
				if( file.image ){
					this.chooseSize(file);
				} else {
					this.useFile(file.url);
				}
			}


            if( this.selectionMode && !this.callback ){
                file.selected = !file.selected;
                if( !this.multiple && this.selectedFiles.length > 1 ){
                    file.selected = !file.selected;
                    this.showError('You can only select one file.');
                }
            }

            if( this.isGallery ){
                if( file.selected ){
                    if( this.galleryItems.indexOf(file) < 0  ){
                        this.galleryItems.push(file);
                    }
                } else {
                    this.galleryItems.splice( this.galleryItems.indexOf(file), 1);
                }

            }

			if( !this.standaloneMode && this.selectionMode && this.inputId.length > 0){

				let input = document.getElementById(this.inputId);
				let preview = document.getElementById(this.previewId);

				if( !this.multiple ){
					input.value = file.url;
					if( this.fileType === 'image' ){
						preview.innerHTML = '';
						let  $img = document.createElement('img');
						$img.setAttribute('class', 'lfm-preview-image');
						$img.setAttribute('src', file.url);
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
                    $input.value = file.url;
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
						$img.setAttribute('src', file.url);
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
            let self = this;
            if( this.isGallery ){
                insertGalleryImagesCallback(this.galleryItems);
            } else {
                window.postMessage({
                      mceAction: 'insertgallery',
                      content: self.selectedFiles
                });
            }

            this.closeFileManager();
        },
		showFileManager(){
			let $fm = document.getElementById('filemanager');
			$fm.classList.add('show');
		},
		closeFileManager(){
			this.closeFileInfoPanel();
            this.isGallery = false;
            this.galleryItems = [];
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
