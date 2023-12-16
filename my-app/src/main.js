import Vue from 'vue'
import App from './App.vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import router from './router'
import BaiduMap from 'vue-baidu-map'
import axios from 'axios';
import Cookie from 'js-cookie';

Vue.use(BaiduMap, {
  // ak 是在百度地图开发者平台申请的密钥 详见 http://lbsyun.baidu.com/apiconsole/key */
  ak: 'uZU4szqkLeeg1CriCLLhT8PpVC4kFvmu'
})

//配置请求根路径
// axios.defaults.baseURL = 'http://119.91.64.37/PHP'

//设置请求拦截器，在每次发起请求时都会执行这个函数
axios.interceptors.request.use(config => {
  console.log(config)
  config.headers.Authorization = window.sessionStorage.getItem('token')
  //在最后必须return config
  return config
})

Vue.prototype.$axios = axios
Vue.config.productionTip = false
Vue.use(ElementUI)


new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
