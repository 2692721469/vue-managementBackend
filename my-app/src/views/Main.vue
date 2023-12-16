<template>
    <div>
        <el-container>
            <el-aside width="190px">
                <CommonAside />
            </el-aside>
            <el-container>
                <el-header>
                    <CommonHead></CommonHead>
                </el-header>
                <el-main>
                    <!-- <h1>main</h1> -->
                    <!-- 路由出口 -->
                    <router-view></router-view>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script>
import CommonAside from '../components/CommonAside.vue';
import CommonHead from '../components/CommonHead.vue';
export default {
    data() {
        return {};
    },
    components: {
        CommonAside,
        CommonHead
    }
};
</script>

<style scoped>
    .el-header{
        padding: 0;
    }
</style>