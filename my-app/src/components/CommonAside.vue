<template>
    <el-menu default-active="home" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose"
        :collapse="isCollapse" background-color="#545f74" text-color="#fff" active-text-color="#fff00f" style="width: auto;">
        <h3>管理系统</h3>
        <el-menu-item @click="clickMenu(item)" v-for="item in noChildren" :key="item.name" v-bind:index="item.name">
            <i :class="`el-icon-${item.icon}`"></i>
            <span slot="title">{{ item.label }}</span>
        </el-menu-item>
        <el-submenu v-for="item in hasChildren" :key="item.label" v-bind:index="item.label">
            <template slot="title">
                <i :class="`el-icon-${item.icon}`"></i>
                <span slot="title">{{ item.label }}</span>
            </template>
            <el-menu-item-group v-for="subItem in item.children" :key="subItem.name">
                <el-menu-item @click="clickMenu(subItem)" v-bind:index="subItem.name">{{ subItem.label }}</el-menu-item>
            </el-menu-item-group>
        </el-submenu>
    </el-menu>
</template>

<script>
export default {
    data() {
        return {
            isCollapse: false,
            menuData: [
                {
                    path: "/",
                    name: "home",
                    label: "首页",
                    icon: "s-home",
                    url: "Home/Home",
                },
                {
                    path: "/dataBoard",
                    name: "dataBoard",
                    label: "数据看板",
                    icon: "s-data",
                    url: "DataBoard/DataBoard",
                },
                {
                    path: "/sysMonitor",
                    name: "sysMonitor",
                    label: "系统监控",
                    icon: "monitor",
                    url: "SysMonitor/SysMonitor",
                },
                {
                    label: "设备管理",
                    icon: "s-grid",
                    children: [
                        {
                            path: "/devManage",
                            name: "devManage",
                            label: "设备管理",
                            icon: "setting",
                            url: "DeviceManage/DevManage",
                        },
                        {
                            path: "/devGroup",
                            name: "devGroup",
                            label: "设备分组",
                            icon: "setting",
                            url: "DeviceManage/DevGroup",
                        },
                    ],
                },
                {
                    label: "运维管理",
                    icon: "s-operation",
                    children: [
                        {
                            path: "/agrManage",
                            name: "agrManage",
                            label: "协议管理",
                            icon: "setting",
                            url: "OpManage/AgrManage",
                        },
                        {
                            path: "/devFirmware",
                            name: "devFirmware",
                            label: "设备固件",
                            icon: "setting",
                            url: "OpManage/DevFirmware",
                        },
                        {
                            path: "/devPosition",
                            name: "devPosition",
                            label: "设备定位",
                            icon: "setting",
                            url: "OpManage/DevPosition",
                        },
                    ],
                },
                {
                    label: "系统设置",
                    icon: "setting",
                    children: [
                        {
                            path: "/sysSetting",
                            name: "sysSetting",
                            label: "系统设置",
                            icon: "el-icon-setting",
                            url: "SysSet/SysSetting",
                        },
                        {
                            path: "/dataManage",
                            name: "dataManage",
                            label: "数据管理",
                            icon: "setting",
                            url: "SysSet/DataManage",
                        },
                        {
                            path: "/collectManage",
                            name: "collectManage",
                            label: "采集管理",
                            icon: "setting",
                            url: "SysSet/CollectManage",
                        },
                        {
                            path: "/sysApi",
                            name: "sysApi",
                            label: "系统接口",
                            icon: "setting",
                            url: "SysSet/SysApi",
                        },
                    ],
                },
            ],
        };
    },
    methods: {
        handleOpen(key, keyPath) {
            console.log(key, keyPath);
        },
        handleClose(key, keyPath) {
            console.log(key, keyPath);
        },
        //点击侧边栏菜单跳转函数
        clickMenu(item) {
            //当当前页面路由与跳转的路由不一致才运行跳转
            if ((this.$route.path !== item.path) && !(this.$route.path === '/home' && (item.path === '/'))) {
                this.$router.push(item.path)
            }
        }
    },
    computed: {
        //没有子菜单
        noChildren() {
            return this.menuData.filter((item) => !item.children);
        },
        //有子菜单
        hasChildren() {
            return this.menuData.filter((item) => item.children);
        },
    },
};
</script>

<style lang="less" scoped>
.el-menu-vertical-demo:not(.el-menu--collapse) {
    width: 200px;
    min-height: 400px;
}

.el-menu {
    height: 100vh;

    h3 {
        color: #fff;
        text-align: center;
        line-height: 48px;
        font-size: 18px;
        font-weight: 400;
    }

}

.el-menu{
    border: 0 !important;
}
</style>

