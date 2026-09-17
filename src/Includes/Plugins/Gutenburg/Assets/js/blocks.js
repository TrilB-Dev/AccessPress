(function (wp) {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, RangeControl, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;

    const controls = (name, attributes, setAttributes) => {
        const fields = [];
        if (name === 'accesspress/wiki-list') {
            fields.push(el(RangeControl, { label: __('Posts per page', 'accesspress'), value: attributes.perPage, min: 1, max: 50, onChange: (perPage) => setAttributes({ perPage }) }));
            fields.push(el(SelectControl, { label: __('Order by', 'accesspress'), value: attributes.orderby, options: [{ label: __('Date', 'accesspress'), value: 'date' }, { label: __('Title', 'accesspress'), value: 'title' }], onChange: (orderby) => setAttributes({ orderby }) }));
            fields.push(el(SelectControl, { label: __('Order', 'accesspress'), value: attributes.order, options: [{ label: 'DESC', value: 'DESC' }, { label: 'ASC', value: 'ASC' }], onChange: (order) => setAttributes({ order }) }));
        }
        if (name === 'accesspress/wiki-related') {
            fields.push(el(RangeControl, { label: __('Related posts', 'accesspress'), value: attributes.limit, min: 1, max: 12, onChange: (limit) => setAttributes({ limit }) }));
        }
        return fields.length ? el(InspectorControls, {}, el(PanelBody, { title: __('Block settings', 'accesspress'), initialOpen: true }, fields)) : null;
    };

    const register = (name, title, icon, attributes, description) => registerBlockType(name, {
        title,
        icon,
        category: 'widgets',
        description,
        attributes,
        edit: ({ attributes: current, setAttributes }) => el(Fragment, {}, controls(name, current, setAttributes), el('div', useBlockProps({ className: 'accesspress-block-placeholder' }), el('strong', {}, title), el('p', {}, __('This AccessPress block renders on the frontend.', 'accesspress')))),
        save: () => null,
    });

    register('accesspress/wiki-breadcrumbs', __('Wiki Breadcrumbs', 'accesspress'), 'admin-links', {}, __('Displays the current Wiki breadcrumb trail.', 'accesspress'));
    register('accesspress/wiki-list', __('Wiki List', 'accesspress'), 'list-view', { perPage: { type: 'number', default: 10 }, orderby: { type: 'string', default: 'date' }, order: { type: 'string', default: 'DESC' } }, __('Displays a list of Wiki posts.', 'accesspress'));
    register('accesspress/wiki-reading-time', __('Wiki Reading Time', 'accesspress'), 'clock', {}, __('Displays the estimated reading time for the current Wiki.', 'accesspress'));
    register('accesspress/wiki-related', __('Wiki Related', 'accesspress'), 'admin-post', { limit: { type: 'number', default: 5 } }, __('Displays related Wiki posts.', 'accesspress'));
    register('accesspress/wiki-toc', __('Wiki Table of Contents', 'accesspress'), 'menu', {}, __('Displays headings from the current Wiki.', 'accesspress'));
    register('accesspress/wiki-search-modal', __('Wiki Search Modal', 'accesspress'), 'search', {}, __('Displays a Wiki search modal.', 'accesspress'));
}(window.wp));


