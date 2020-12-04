## S360 Toolkit: Paragraphs

Required modules:

- Core
  - Media
  - Media Library
  - Views
- Contributed
  - [Paragraphs](https://www.drupal.org/project/paragraphs)
  - [Field Group](https://www.drupal.org/project/field_group)
  - [Webform](https://www.drupal.org/project/webform)
  - [Views Reference Field](https://www.drupal.org/project/viewsreference)

Install the contributed modules using composer:

$ `composer require drupal/paragraphs drupal/viewsreference drupal/webform drupal/field_group`

Enabled the core modules

$ `drush en media media_library views -y`

---

## S360 Toolkit: Layout Builder

Required modules:

- Contributed
  - [Paragraphs](https://www.drupal.org/project/paragraphs)
  - [Layout Paragraphs](https://www.drupal.org/project/layout_paragraphs)

The "Layout Paragraphs" module requires the core module "Layout Discovery". 
Because of this, additional layouts will be added.  The "S360 Toolkit: Layout 
Builder" aims to remove these layouts as well as any layouts from the "Layout 
Builder" core module (if enabled).

Install the contributed modules using composer:

$ `composer require drupal/paragraphs drupal/layout_paragraphs -y`


---

$ `composer require drupal/address`

$ `composer require drupal/admin_toolbar`

$ `composer require drupal/admin_toolbar_mediteran`

$ `composer require drupal/allowed_formats`

$ `composer require drupal/better_exposed_filters`

$ `composer require drupal/ckeditor_codemirror`

$ `composer require drupal/config_split`

$ `composer require drupal/ctools`

$ `composer require drupal/devel`

$ `composer require drupal/double_field`

$ `composer require drupal/editor_advanced_link`

$ `composer require drupal/focal_point`

$ `composer require drupal/google_tag`

$ `composer require drupal/inline_entity_form`

$ `composer require drupal/layout_paragraphs`

$ `composer require drupal/media_library_edit`

$ `composer require drupal/media_library_form_element`

$ `composer require drupal/media_pdf_thumbnail`

$ `composer require drupal/media_thumbnails`

$ `composer require drupal/metatag`

$ `composer require drupal/module_filter`

$ `composer require drupal/pathauto`

$ `composer require drupal/pcr`

$ `composer require drupal/permission_ui`

$ `composer require drupal/pfm`

$ `composer require drupal/redirect`

$ `composer require drupal/schema_metatag`

$ `composer require drupal/simplei`

$ `composer require drupal/smart_date`

$ `composer require drupal/smart_trim`

$ `composer require drupal/structure_sync`

$ `composer require drupal/token`

$ `composer require drupal/toolbar_menu`

$ `composer require drupal/twig_tweak`

$ `composer require drupal/view_unpublished`

$ `composer require drupal/views_bulk_edit`

$ `composer require drupal/views_bulk_operations`

$ `composer require drush/drush`