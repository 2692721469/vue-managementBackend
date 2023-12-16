<template>
    <div class="header-container">
        <div class="l-content">
            <el-button icon="el-icon-menu" size="mini"></el-button>
            <!-- 面包屑 -->
            <!-- <span class="text">首页</span> -->
        </div>
        <div class="r-content">
            <el-dropdown @command="logout">
                <span type="el-dropdowm-link">
                    <img class="user" src="../assets/images/user.png" alt="">
                </span>
                <el-dropdown-menu>
                    <el-dropdown-item command="userCenter">个人中心</el-dropdown-item>
                    <el-dropdown-item command="logout">退出登录</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    },
    methods: {
        logout(command) {
            //如果点击的是推出登录
            if (command === 'logout') {
                console.log(command);
                window.sessionStorage.clear();
                this.$router.push('/login');
            } else if (command === 'userCenter') {
                console.log(command);
                this.$router.push('/User');
            }
        }
    }
};
</script>

<style lang="less" scoped>
.header-container {
    padding: 0 20px;
    background-color: #333;
    height: 60px;
    display: flex;
    justify-content: space-between; //横轴上
    align-items: center; //纵轴上

    .text {
        color: #fff;
        font-size: 14px;
        margin-left: 20px;
    }

    .r-content {
        .user {
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }
    }
}
</style>