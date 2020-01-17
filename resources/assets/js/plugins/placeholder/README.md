# tinymce-placeholder
Placeholder plugin for TinyMCE

## Use
Add "placeholder" to plugin option for tinymce and either have placeholder attribute on the element or set the option placeholder when initating tinymce.

```
tinymce.init({
  placeholder: "Placeholder from settings",
  plugins: ['placeholder'],
});
```

Based on https://github.com/angular-ui/ui-tinymce/issues/197 @human-a
