require('./bootstrap');

import Vue from 'vue';
import VueJsModal from 'vue-js-modal';

import Flash from './components/Flash.vue';
import FeatherIcon from './components/FeatherIcon.vue';
import VueTypeahead from './components/VueTypeahead.vue';
import CourseUpdateModal from "./components/CourseUpdateModal.vue";
import PaperUpdateModal from './components/PaperUpdateModal.vue';
import LettersSearchFilters from './components/LettersSearchFilters.vue';
import CollegeUpdateModal from "./components/CollegeUpdateModal.vue";
import UserUpdateModal from './components/UserUpdateModal.vue';

Vue.use(VueJsModal);

window.Events = new Vue();

Vue.component('v-flash', Flash);
Vue.component('feather-icon', FeatherIcon);
Vue.component('vue-typeahead', VueTypeahead);
Vue.component("course-update-modal", CourseUpdateModal);
Vue.component("paper-update-modal", PaperUpdateModal);
Vue.component('letter-search-filters', LettersSearchFilters);
Vue.component("college-update-modal", CollegeUpdateModal);
Vue.component("user-update-modal", UserUpdateModal);

const app = new Vue({
    el: '#app'
});
