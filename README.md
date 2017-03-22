# Gravity Forms Slideshow
This plugin allows users to contribute to an image slideshow by uploading pictures through a Gravity Forms form. Images that are uploaded (and approved) can be displayed as a [Slick Slider](https://kenwheeler.github.io/slick/) slideshow using the provided shortcode.  The slideshow also has lightbox functionality using [Slick Lightbox](https://github.com/mreq/slick-lightbox). 

## Requirements
PHP version 5.3+

## Installation
Download the zip file and install it just like any other WordPress plugin.

## Setup
Once activated you can find the plugin settings page under the Settings menu.

### Settings
Use the settings page form to map the image and caption fields of the Gravity form where users will be uploading images.

### Shortcode
The slideshow is rendered using a shortcode.  Copy the shortcode found on the settings page and paste it where you want the slideshow to appear.  You can pass several arguments in order to customize the display:
- gform_id: The ID of the Gravity form that is being used to generate the slideshow.
- max_slides: Default is unlimited.  You can limit the number of slides (say, 10) by passing `max_slides=10`.
- show_captions: Default is false.  You can enable captions on slides by passing `show_captions="true"`.
- slider_syncing: See https://kenwheeler.github.io/slick/ for an example of slider syncing. Default is false. You can enable slider syncing by passing `slider_syncing="true"`.
- show_unapproved: By default all images must be approved before they will appear in the slideshow.  You can bypass this by passing `show_unapproved="true"`.

Here is an example shortcode with all of the options specified.  Remember, `gform_id` is the only required parameter: 
```
[gf-slideshow gform_id="1" max_slides=10 show_captions="true" slider_syncing="true" show_unapproved="true"]
```

### Approving Images
By default only images that are marked as approved will appear in the slideshow.  To approve images, go to the image/attachment edit screen and select "Yes" in the "Approve user upload" metabox.  Note, if you don't see the metabox, click on "Edit more details" link to bring you to the image edit screen.

Image edit links can be included in Gravity Forms notification emails by adding the merge tag `{image_edit_links}`.

## Hooks and overrides
- Filters:
```
// Filters the alt text prefix for the images. Default is 'User upload: '.
add_filter( 'gf_slideshow_alt_text_prefix', $alt_text_prefix, 10, 1 );
```
```
// Filters the user supplied image caption.
add_filter( 'gf_slideshow_caption', $caption, 10, 1 );
```
- Overrides:
Slideshow view files (templates) can be overridden by copying them into a folder called 'gf-slideshow' within your active theme.  Ensure that the file names remains the same, otherwise the plugin will not be able to locate them.

