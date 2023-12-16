import Vue from 'vue'
import VueRouter from 'vue-router'
//1\创建路由组件
import Home from '../views/Home.vue'
import User from '../views/User.vue'
import Main from '../views/Main.vue'
import DataBoard from '../views/DataBoard.vue'
import SysMonitor from '../views/SysMonitor.vue'
import DevManage from '../views/DevManage.vue'
import DevGroup from '../views/DevGroup.vue'
import AgrManage from '../views/AgrManage.vue'
import DevFirmware from '../views/DevFirmware.vue'
import DevPosition from '../views/DevPosition.vue'
import SysSetting from '../views/SysSetting.vue'
import DataManage from '../views/DataManage.vue'
import CollectManage from '../views/CollectManage.vue'
import SysApi from '../views/SysApi.vue'
import Login from '../views/Login.vue'




Vue.use(VueRouter)

//2\映射
const routes = [
    //主路由
    {
        path: '/',
        component: Main,
        redirect: '/home',//页面重定向
        children: [
            //子路由
            { path: 'home', component: Home },  //首页
            { path: 'user', component: User },  //用户管理
            { path: 'dataBoard', component: DataBoard },    //数据看板
            { path: 'sysMonitor', component: SysMonitor },  //系统监控
            { path: 'devManage', component: DevManage },    //设备管理
            { path: 'devGroup', component: DevGroup },      //设备分组
            { path: 'agrManage', component: AgrManage },    //协议管理
            { path: 'devFirmware', component: DevFirmware },//设备固件
            { path: 'devPosition', component: DevPosition },//设备定位
            { path: 'sysSetting', component: SysSetting },  //系统设置
            { path: 'dataManage', component: DataManage },  //数据管理
            { path: 'collectManage', component: CollectManage },//采集管理
            { path: 'sysApi', component: SysApi }           //接口管理
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    }
]

//3\创建router实例
const router = new VueRouter({
    routes //缩写，相当于routes:routes
})


//挂载路由导航守卫
router.beforeEach((to, from, next) => {
    //to 将要访问的路径
    //from 代表从哪个页面跳转来
    //next 是一个函数，next()放行,next('/home')强制跳转
    if (to.path === '/login') return next();
    //获取token
    const tokenStr = window.sessionStorage.getItem('token');
    if (!tokenStr) return next('/login');
    next();
})
//对外进行暴露
export default router