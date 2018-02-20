# Frontend Router

Make requests with `_wrapper_format=frontend_router` to get responses for a
frontend JS-powered router.

## Theming

The response renders with `html__fragment` and `page__fragment` as theme hook
suggestions so that themes can modify the response how they like.

Changable regions are also wrapped in `router-content` HTML tags with the `area`
tag being the ID of the region. This can be used to identify them and the tag
also gives a consistent parent for manipulation. Regions can be marked as
_changeable_, from the theme's settings page.

## Gotchas

To avoid duplication of returned assets/libraries, `ajax_page_state` should be
passed in the request query, as per Drupal core AJAX requests (see
`misc/ajax.es6.js` in core as an example).
