import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';


import List from '@/components/Main/List'
import Dialog from '@/components/Validation/MessageValidation'




Vue.component('app-list', List)
Vue.component('app-dialog', Dialog)


Vue.config.productionTip = false

new Vue({
  vuetify,
  render: h => h(App)
}).$mount('#app')
