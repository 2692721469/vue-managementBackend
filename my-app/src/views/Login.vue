<template>
    <div class="login-register-container">
        <el-card class="login-register-card">
            <el-tabs v-model="activeTab" type="card">
                <el-tab-pane label="登录" name="login">
                    <el-form ref="loginFormRef" :model="loginForm" label-width="80px">
                        <el-form-item label="用户名:" prop="username" :rules="[
                            { required: true, message: '请输入用户名', trigger: 'blur' }
                        ]">
                            <el-input v-model="loginForm.username" placeholder="请在此处输入用户名/邮箱..."></el-input>
                        </el-form-item>
                        <el-form-item label="密 码:" prop="password" :rules="[
                            { required: true, message: '请输入密码', trigger: 'blur' }
                        ]">
                            <el-input type="password" v-model="loginForm.password" show-password
                                placeholder="请在此处输入密码..."></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="info" @click="restFrom" round>重置</el-button>
                            <el-button type="primary" @click="login" round>登录</el-button>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane label="注册" name="register">
                    <el-form ref="registerForm" :model="registerForm" label-width="80px">
                        <el-form-item label="用户名:" prop="username">
                            <el-input v-model="registerForm.username" placeholder="请输入用户名..." :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item label="密 码:" prop="password">
                            <el-input type="password" v-model="registerForm.password" show-password placeholder="请输入密码..."
                                :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item label="姓 名:" prop="name">
                            <el-input type="name" v-model="registerForm.name" placeholder="请输入注册人姓名..."
                                :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item label="电 话:" prop="telephone">
                            <el-input type="telephone" v-model="registerForm.telephone" placeholder="请输入电话号码..."
                                :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item label="邮 箱:" prop="mail">
                            <el-input type="mail" v-model="registerForm.mail" placeholder="请输入邮箱..."
                                :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item label="验证码:" prop="code">
                            <el-input type="code" v-model="registerForm.code" placeholder="请输入验证码..."
                                :disabled="true"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="register" :disabled="true"
                                style="width: 150px;">注册</el-button>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>
  
<script>
import Cookie from 'js-cookie';
export default {
    data() {
        return {
            activeTab: 'login',
            token: '',
            loginForm: {
                username: '',
                password: ''
            },
            registerForm: {
                username: '',//用户名
                password: '',//密码
                telephone: '',//电话号码
                mail: '',//邮箱
                name: '',//姓名
                code: '',//验证码
            }
        };
    },
    methods: {
        login() {
            // 在这里实现登录逻辑
            if (this.loginForm.username !== '') {
                if (this.loginForm.password !== '') {
                    console.log('登录');
                    // 发送登录请求到后端
                    this.$axios.post('PHP/login.php', this.loginForm)
                        .then(response => {
                            if (response.data.success) {
                                // 登录成功，接收并存储令牌
                                window.sessionStorage.setItem("token", response.data.token);
                                // Cookie.set('token', response.data.token);
                                console.log('token:', response.data.token);
                                this.$message.success('登录成功！');
                                setTimeout(() => {
                                    this.$router.push('/home');
                                }, 1000);
                            } else {
                                console.error('登录失败:', response.data.message);
                                // 在登录失败时，你可以使用 this.$message 或其他方式来显示错误消息
                                this.$message.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            // 登录失败，处理错误
                            console.error('登录失败:', error);
                            // 在捕获到异常时，也可以使用 this.$message 或其他方式来显示错误消息
                            this.$message.error('登录失败');
                        });
                } else {
                    this.$message('密码不能为空');
                }
            } else {
                this.$message('账号不能为空');
            }
        },

        register() {
            // 在这里实现注册逻辑
            console.log('注册');
        },

        restFrom() {
            this.$refs.loginFormRef.resetFields();
            // console.log(this);
        }
    }
};
</script>
  
<style lang="less" scoped>
.login-register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-register-card {
    width: 400px;
    padding: 20px;
    border-radius: 10px;
}
</style>
  