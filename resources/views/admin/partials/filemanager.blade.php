<div id="filemanager">
	<div class="fm-inner">
        <div id="fm-uploading" v-if="fmUploading" v-cloak><i class="fal fa-circle-notch fa-spin"></i> Uploading and processing</div>
		<a class="close-file-manager" role="button" @click.prevent="closeFileManager" href="/">&times;</a>
		<div class="filemanager">
			<div class="fm-toolbar">
				<a href="/" title="Create folder" @click.prevent="showCreateFolderModal()"><i class="fal fa-folder-plus fa-fw"></i></a>
				Selected Files: @{{ selectedFiles.length }}
                <a href="#" class="use-chosen-btn" @click.prevent="selectAllImages()" v-if="isGallery">Select/Deselect All</a>
                <a class="use-chosen-btn" v-if="selectedFiles.length > 0" href="#" @click.prevent="useChosenFiles()" role="button" v-cloak>Use Chosen File@{{ selectedFiles.length > 1 ? 's' : '' }}</span></a>
			</div>
			<div class="fm-path-wrap">
				Current Path:
				<span class="fm-path-crumbs">
					<span v-for="(path, index) in paths"><span class="path-sep">/</span><a class="path-crumb" v-if="index < paths.length-1"  href="/" @click.prevent="gotoPath(path)">@{{ path.name }} <i v-if="path.loading" class="fal fa-circle-notch fa-spin" v-cloak></i></a><span v-if=" paths.length-1 === index ">@{{ path.name }}</span>
					</span>
				</span>
			</div>
			<div class="fm-messages" :class="{ show: errors.length > 0 }">
				<ul class="fm-messages-list">
					<li v-for="error in errors">@{{ error.message }}</li>
				</ul>
			</div>
			<ul class="folders-files-list">
				<li v-for="(item, index) in items.folders" class="fm-type folder">
					<a class="fm-delete-item" href="/" @click.prevent="deleteFolder(item)">&times;</a>
					<a href="/" class="fm-list-inner" @click.prevent="gotoPath(item)">
						<i class="fas fa-folder"></i>
						<div class="fm-folder-name">@{{ item.path | itemName }}</div>
					</a>
                    <div v-if="item.loading" class="folder-loader" v-cloak>
                        <i class="fal fa-circle-notch fa-spin"></i>
                    </div>
				</li>
				<li v-for="(file, index ) in items.files" :key="file.id" class="fm-type file">
					<a v-if="file.image" class="fm-edit-item" href="/" @click.prevent="showEditImage(file, index)"><i class="fal fa-pencil-ruler"></i></a>
					<a class="fm-delete-item" href="/" @click.prevent="deleteFile(file)">&times;</a>
					<div class="fm-list-inner" :class="{ selected: file.selected }" @click="selectFile(file)">
						<img v-if="file.image" :id="'image-'+index" src="" :data-original-image="file.sizes.original" style="display: none">
						<img v-if="file.image" :src="file.url">
						<div v-if="!file.image" class="fm-file-icon">
							<span>@{{ file.info.extension }}</span>
							<i class="fal fa-file"></i>
						</div>
						<div class="fm-folder-name">@{{ file.info.basename }}</div>
					</div>
				</li>
			</ul>
		</div>
		<div v-if="editImageModal" class="fm-modal edit-image-modal" v-cloak>
			<div class="fm-modal-inner">
				<a class="close-fm-modal" href="/" role="button" @click.prevent="closeEditImageModal">&times;</a>
				<div class="cropper-panel">
					<vue-cropper
					  ref="cropper"
					  :src="imgSrc"
					  :auto-crop=false
					  preview=".img-preview"
					>
					</vue-cropper>
					<div class="cropper-sidebar">
						<div class="img-preview"></div>
					</div>
				</div>
				<div class="fm-drop-actions">
					<a href="#" role="button" @click.prevent="zoom(0.2)" title="zoom in"><i class="fal fa-search-plus fa-fw"></i></a>
					<a href="#" role="button" @click.prevent="zoom(-0.2)" title="zoom out"><i class="fal fa-search-minus fa-fw"></i></a>
					<a href="#" role="button" @click.prevent="rotate(90)" title="rotate"><i class="fal fa-sync-alt fa-fw"></i></a>
					<a ref="flipX" href="#" role="button" @click.prevent="flipX"><i class="fal fa-arrows-h fa-fw"></i></a>
					<a ref="flipY" href="#" role="button" @click.prevent="flipY"><i class="fal fa-arrows-v fa-fw"></i></a>
					<a href="#" role="button" @click.prevent="reset" title="undo"><i class="fal fa-undo fa-fw"></i></a>
					<a href="#" role="button" @click.prevent="savedEditedImage" title="Save"><strong>Save Changes <i v-if="savingEdit" class="fas fa-circle-notch fa-spin"></i></strong></a>
					<!--<a href="#" role="button" @click.prevent="getData">Get Data</a>
					<a href="#" role="button" @click.prevent="setData">Set Data</a>
					<a href="#" role="button" @click.prevent="getCropBoxData">Get CropBox Data</a>
					<a href="#" role="button" @click.prevent="setCropBoxData">Set CropBox Data</a>-->
				 </div>
			 </div>
		</div>
		<vue-dropzone
			ref="myVueDropzone"
			id="dropzone"
			@vdropzone-upload-progress="dzUploadProgress"
			:options="dropzoneOptions"
			@vdropzone-file-added="dzFileAdded"
			@vdropzone-sending-multiple="dzSendingFiles"
			@vdropzone-sending="dzSending"
            @vdropzone-queue-complete="dzQueueComplete"
            @vdropzone-complete="dzComplete"
			@vdropzone-success-multiple="dzSuccess"
            @vdropzone-error="dzError"
			>
		</vue-dropzone>

		<div class="fm-upload-progress"><div class="inner" :style="{ width: uploadPercent + '%' }"></div></div>

		<div v-if="showFileInfoPanel" class="file-info-panel" v-cloak>
			<div class="inner">
				<a href="#" role="button" class="close-file-info-panel" @click.prevent="closeFileInfoPanel">Close</a>
				<h3>File Info</h3>
				<p>
					<strong>Thumb URL:</strong><br>
					@{{ selectedFile.url }}
				</p>
				<div v-if="selectedFile.image">
					<h3>Sizes</h3>
					<ul class="sizes-list">
						<li style="margin-bottom: 12px;" v-for="(size, name) in selectedFile.sizes"><strong>@{{ name }}</strong><br>@{{size}}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div v-if="showSizeChooser" class="size-chooser" v-cloak>
		<div class="inner">
			<a href="/" class="close-size-chooser" role="button" @click.prevent="closeSizeChooser">&times;</a>
			<h4>Choose Size</h4>
			<ul class="sizes-list">
				<li v-for="(size, name) in sizeOptions"><a href="/" role="button" @click.prevent="useFile(size)">@{{ name }}</a></li>
			</ul>
		</div>
	</div>
	<div v-if="folderModal" class="fm-modal folder-upload-modal" v-cloak>
		<div class="fm-modal-inner">
			<a class="close-fm-modal" href="/" @click.prevent="closeUploadFolderModal">&times;</a>
			<p>
				<label for="new-folder-name">New Folder Name</label>
				<input type="text" v-model="folderName">
			</p>
			<button type="button" class="modal-btn" @click.prevent="createFolder">Create Folder</button>
		</div>
	</div>
	<div class="fm-overlay" v-if="showOverlay" v-cloak></div>
</div>
