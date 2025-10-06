<template>
    <component
        :is="tag"
        :type="tag === 'button' ? 'button' : false"
        :href="tag === 'a' ? '#' : false"
        @click.prevent="layout(action)">
        <slot></slot>
    </component>
</template>
  
<script>
import { useAppBaseStore } from '@/stores/base';
export default {
    name: 'LayoutModifier',
    props: {
        tag: {
            type: String,
            default: 'button',
            description: 'The HTML tag of the component (button, a)'
        },
        size: {
            type: String,
            description: 'Button size (sm, lg)'
        },
        variant: {
            type: String,
            default: 'primary',
            description: 'Button variant (primary, alt-primary, outline-primary, secondary, alt-secondar' +
                    'y, outline-secondary, light, alt-light, outline-light, dark, alt-dark, outline' +
                    '-dark, danger, alt-danger, outline-danger, info, alt-info, outline-info, succe' +
                    'ss, alt-success, outline-success, warning, alt-warning, outline-warning, dual)'
        },
        action: {
            type: String,
            description: 'Specify the layout modifier mode to apply on click'
        }
    },
    data(){
        return {
            appBase : useAppBaseStore(),
        }
    },
    methods: {
        layout(action) {
            // Set up layout API
            let layoutAPI = {
                sidebarOpen: () => this.appBase.sidebar('open'),
                sidebarClose: () => this.appBase.sidebar('close'),
                sidebarToggle: () => this.appBase.sidebar('toggle'),
                sidebarPositionLeft: () => this
                    .$store
                    .commit('sidebarPosition', {mode: 'left'}),
                sidebarPositionRight: () => this
                    .$store
                    .commit('sidebarPosition', {mode: 'right'}),
                sidebarPositionToggle: () => this
                    .$store
                    .commit('sidebarPosition', {mode: 'toggle'}),
                sidebarStyleDark: () => this
                    .$store
                    .commit('sidebarStyle', {mode: 'dark'}),
                sidebarStyleLight: () => this
                    .$store
                    .commit('sidebarStyle', {mode: 'light'}),
                sidebarStyleToggle: () => this
                    .$store
                    .commit('sidebarStyle', {mode: 'toggle'}),
                sidebarMiniOn: () => this.appBase.sidebarMini('on'),
                sidebarMiniOff: () => this.appBase.sidebarMini('off'),
                sidebarMiniToggle: () => this.appBase.sidebarMini('toggle'),
                sideOverlayOpen: () => this
                    .$store
                    .commit('sideOverlay', {mode: 'open'}),
                sideOverlayClose: () => this
                    .$store
                    .commit('sideOverlay', {mode: 'close'}),
                sideOverlayToggle: () => this
                    .$store
                    .commit('sideOverlay', {mode: 'toggle'}),
                sideOverlayHoverOn: () => this
                    .$store
                    .commit('sideOverlayHover', {mode: 'on'}),
                sideOverlayHoverOff: () => this
                    .$store
                    .commit('sideOverlayHover', {mode: 'off'}),
                sideOverlayHoverToggle: () => this
                    .$store
                    .commit('sideOverlayHover', {mode: 'toggle'}),
                headerFixed: () => this
                    .$store
                    .commit('header', {mode: 'fixed'}),
                headerStatic: () => this
                    .$store
                    .commit('header', {mode: 'static'}),
                headerToggle: () => this
                    .$store
                    .commit('header', {mode: 'toggle'}),
                headerStyleDark: () => this
                    .$store
                    .commit('headerStyle', {mode: 'dark'}),
                headerStyleLight: () => this
                    .$store
                    .commit('headerStyle', {mode: 'light'}),
                headerStyleToggle: () => this
                    .$store
                    .commit('headerStyle', {mode: 'toggle'}),
                headerSearchOn: () => this
                    .$store
                    .commit('headerSearch', {mode: 'on'}),
                headerSearchOff: () => this
                    .$store
                    .commit('headerSearch', {mode: 'off'}),
                headerSearchToggle: () => this
                    .$store
                    .commit('headerSearch', {mode: 'toggle'}),
                headerLoaderOn: () => this
                    .$store
                    .commit('headerLoader', {mode: 'on'}),
                headerLoaderOff: () => this
                    .$store
                    .commit('headerLoader', {mode: 'off'}),
                headerLoaderToggle: () => this
                    .$store
                    .commit('headerLoader', {mode: 'toggle'}),
                pageLoaderOn: () => this
                    .$store
                    .commit('pageLoader', {mode: 'on'}),
                pageLoaderOff: () => this
                    .$store
                    .commit('pageLoader', {mode: 'off'}),
                pageLoaderToggle: () => this
                    .$store
                    .commit('pageLoader', {mode: 'toggle'}),
                pageOverlayOn: () => this
                    .$store
                    .commit('pageOverlay', {mode: 'on'}),
                pageOverlayOff: () => this
                    .$store
                    .commit('pageOverlay', {mode: 'off'}),
                pageOverlayToggle: () => this
                    .$store
                    .commit('pageOverlaypageOverlay', {mode: 'toggle'}),
                mainContentFull: () => this
                    .$store
                    .commit('mainContent', {mode: 'full'}),
                mainContentBoxed: () => this
                    .$store
                    .commit('mainContent', {mode: 'boxed'}),
                mainContentNarrow: () => this
                    .$store
                    .commit('mainContent', {mode: 'narrow'})
            }

            // Call layout API if action is valid
            if (layoutAPI[action]) {
                layoutAPI[action]()
            }
        }
    }
}
</script>@/stores/base