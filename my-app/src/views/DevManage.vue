<template>
    <el-card body-style="padding-bottom: 0;">
        <el-divider content-position="left">搜索/添加设备</el-divider>
        <el-row style="border-radius: 5px;">
            <el-col :span="18">
                <el-form :model="searchData" ref="searchData" label-width="100px">
                    <el-form-item label="设备SN编号" prop="SNnumber" :rules="[
                        { required: true, message: 'SN编号为空是查询所有设备', trigger: 'blur' },
                    ]">
                        <el-col :span="18"><el-input v-model="searchData.SNnumber" placeholder="请输入设备SN编号..."
                                clearable></el-input></el-col>
                        <el-col :span="6" style="display: flex;justify-content: center;"><el-button type="primary"
                                @click="searchDevice(searchData.SNnumber)" :loading="false">搜索</el-button></el-col>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span="6">
                <div>
                    <el-button type="primary" @click="dialogFormVisible = true">添加设备</el-button>
                    <el-dialog title="添加设备" :visible.sync="dialogFormVisible">
                        <el-form :model="adForm">
                            <el-form-item label="设备SN编号：" :label-width="formLabelWidth">
                                <el-input v-model="adForm.SNsn" placeholder="请输入设备SN编号..." autocomplete="off"></el-input>
                            </el-form-item>

                            <el-form-item label="设备IMEI编号：" :label-width="formLabelWidth">
                                <el-input v-model="adForm.IMEI" placeholder="请输入设备IMEI编号..." autocomplete="off"></el-input>
                            </el-form-item>

                            <el-form-item label="设备启动检测时间：" :label-width="formLabelWidth + 15">
                                <div class="block">
                                    <el-date-picker v-model="adForm.firstDetectionTime" type="datetime" placeholder="选择日期时间"
                                        align="right" :picker-options="pickerOptions">
                                    </el-date-picker>
                                    <span style="padding-left: 30px;">每次采样间隔时间（小时）：</span>
                                    <template>
                                        <el-input-number v-model="adForm.timeInterval" :precision="1" :step="0.5" :min="0"
                                            size="medium" :max="24"></el-input-number>
                                    </template>
                                </div>
                            </el-form-item>

                            <el-form-item label="是否启用设备" :label-width="formLabelWidth">
                                <el-switch v-model="adForm.On" active-text="开启" inactive-text="关闭"
                                    style="margin-left: 15px;"></el-switch>
                            </el-form-item>

                            <el-form-item label="备注内容：" :label-width="formLabelWidth">
                                <el-input type="textarea" placeholder="请输入设备备注..." v-model="adForm.notes" maxlength="15"
                                    show-word-limit>
                                </el-input>
                            </el-form-item>

                            <el-form-item label="设备分类：" :label-width="formLabelWidth">
                                <el-select v-model="adForm.classification" placeholder="请选择设备分类">
                                    <el-option label="总磷" value="TP"></el-option>
                                    <el-option label="总氮" value="TN"></el-option>
                                    <el-option label="硫化物" value="S"></el-option>
                                </el-select>
                            </el-form-item>

                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="dialogFormVisible = false">取 消</el-button>
                            <el-button type="primary" @click="addDevice()" :loading="addConfirm">确 定</el-button>
                        </div>
                    </el-dialog>
                </div>
            </el-col>
        </el-row>

        <el-row>
            <el-divider content-position="left">设备列表</el-divider>
            <el-table ref="deviceTable" :data="devices" highlight-current-row @current-change="handleCurrentChange"
                style="width: 100%" v-loading="devLoading">
                <el-table-column type="index" width="50">
                </el-table-column>
                <el-table-column property="sn_number" label="设备SN编号" width="200">
                </el-table-column>
                <el-table-column property="imei_number" label="设备IMEI编号" width="200">
                </el-table-column>
                <el-table-column property="category" label="设备分类" width="150">
                </el-table-column>
                <el-table-column property="notes" label="设备备注" width="300">
                </el-table-column>
                <el-table-column property="online_status" label="设备在线状态" width="180">
                </el-table-column>
                <el-table-column property="whetherOn" label="是否启用设备" width="170">
                </el-table-column>
                <el-table-column width="100">
                    <template slot-scope="scopeEdit">
                        <el-button type="primary" @click="editDevice(scopeEdit.row)" size="mini">编辑设备</el-button>
                    </template>
                </el-table-column>
                <el-table-column>
                    <template slot-scope="scopeDel">
                        <el-button type="danger" @click="delDevice(scopeDel.row)" size="mini">删除设备</el-button>
                    </template>
                    <!-- <el-button type="primary" @click="delDevice()" size="mini">删除设备</el-button> -->
                </el-table-column>
            </el-table>

            <el-dialog title="编辑设备" :visible.sync="editdialogFormVisible">
                <el-form :model="form">
                    <el-form-item label="设备SN编号：" :label-width="formLabelWidth">
                        <el-input v-model="form.SNsn" placeholder=form.SNsn autocomplete="off" :disabled="true"></el-input>
                    </el-form-item>

                    <el-form-item label="设备IMEI编号：" :label-width="formLabelWidth">
                        <el-input v-model="form.IMEI" placeholder=form.IMEI autocomplete="off" :disabled="true"></el-input>
                    </el-form-item>
                    <el-form-item label="设备启动检测时间：" :label-width="formLabelWidth + 15">
                        <div class="block">
                            <el-date-picker v-model="form.firstDetectionTime" type="datetime" placeholder="选择日期时间"
                                align="right" :picker-options="pickerOptions">
                            </el-date-picker>
                            <span style="padding-left: 30px;">每次采样间隔时间（小时）：</span>
                            <template>
                                <el-input-number v-model="form.timeInterval" :precision="1" :step="0.5" :min="0"
                                    size="medium" :max="24"></el-input-number>
                            </template>
                        </div>
                    </el-form-item>
                    <el-form-item label="是否启用设备" :label-width="formLabelWidth">
                        <el-switch v-model="form.On" active-text="开启" inactive-text="关闭"
                            style="margin-left: 40px;"></el-switch>
                    </el-form-item>

                    <el-form-item label="备注内容：" :label-width="formLabelWidth">
                        <el-input type="textarea" placeholder="请输入设备备注..." v-model="form.notes" maxlength="15"
                            show-word-limit>
                        </el-input>
                    </el-form-item>

                    <el-form-item label="设备分类：" :label-width="formLabelWidth">
                        <el-select v-model="form.classification" placeholder="请选择设备分类">
                            <el-option label="总磷" value="TP"></el-option>
                            <el-option label="总氮" value="TN"></el-option>
                            <el-option label="硫化物" value="S"></el-option>
                        </el-select>
                    </el-form-item>

                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="editdialogFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="saveDevice()" :loading="editConfirm">确 定</el-button>
                </div>
            </el-dialog>

        </el-row>
    </el-card>
</template>

<script>
import Cookie from 'js-cookie';
export default {
    data() {
        return {
            editConfirm: false,//编辑设备表单确认按钮加载状态标志
            addConfirm: false,//添加设备表单确认按钮加载状态标志
            devLoading: true,//设备列表加载状态标志
            devices: [], // 存储设备信息的数组
            searchData: {
                SNnumber: ''
            },
            dialogTableVisible: false,
            dialogFormVisible: false,
            editdialogFormVisible: false,
            adForm: {//添加设备表单
                On: true,
                SNsn: '',
                IMEI: '',
                classification: '',
                notes: '',
                firstDetectionTime: '',
                timeInterval: ''
            },
            form: {//编辑设备表单
                On: true,
                SNsn: '',
                IMEI: '',
                classification: '',
                notes: '',
                firstDetectionTime: '',
                timeInterval: ''
            },
            formLabelWidth: '120px',
            currentRow: null
        };
    },
    methods: {
        searchDevice(formName) {
            this.devLoading = true;
            setTimeout(() => {
                console.log(formName);
                const vm = this; // 引用 Vue 组件实例
                // 在Vue组件中使用Axios来获取设备列表
                if (formName == '') {
                    // 获取 JWT 令牌（从 Cookie 或其他存储中获取）
                    const token = Cookie.get('token'); // 从 Cookie 或其他存储中获取 JWT 令牌
                    console.log('cookie:'.token)
                    // 设置 "Authorization" 头部，包括 JWT 令牌
                    this.$axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    this.$axios.get(`http://119.91.64.37/PHP/searchDeviceAll.php`).then(function (response) {
                        console.log(response);
                        vm.devices = response.data; // 将获取到的数据赋值给devices属性
                        // 刷新表格
                        vm.$nextTick(() => {
                            vm.$refs.deviceTable.doLayout();
                        });
                    }).catch(function (error) {
                        console.log(error);
                        vm.$message({
                            showClose: true,
                            message: '服务器异常，查询设备列表失败！',
                            type: 'error'
                        });
                    });
                } else {
                    this.$axios.get(`http://119.91.64.37/PHP/searchDevice.php?sn_device=${this.searchData.SNnumber}`).then(function (response) {
                        console.log(response);
                        vm.devices = response.data; // 将获取到的数据赋值给devices属性
                        // 刷新表格
                        vm.$nextTick(() => {
                            vm.$refs.deviceTable.doLayout();
                        });
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            }, 500);
            this.devLoading = false;// 隐藏加载状态
        },
        addDevice() {
            this.addConfirm = true;//开启确认按钮加载状态
            // 发送表单数据到后端
            this.$axios.post('http://119.91.64.37/PHP/addDevice.php', JSON.stringify(this.adForm))
                .then(response => {
                    // 处理成功响应
                    console.log('添加设备成功!', response);
                    this.dialogFormVisible = false; // 关闭编辑弹窗
                    // 可以刷新设备列表或更新设备数据
                    // ...
                    this.$message({
                        showClose: true,
                        message: '添加设备成功!',
                        type: 'success'
                    });
                    this.dialogFormVisible = false//不可见弹窗
                })
                .catch(error => {
                    // 处理错误响应
                    console.error('添加设备失败!', error);
                    // 可以显示错误消息
                    // ...
                    this.$message({
                        showClose: true,
                        message: '添加设备失败!',
                        type: 'error'
                    });
                });

            setTimeout(() => {
                this.searchDevice();//重新加载设备列表
                this.addConfirm = false;//关闭确认按钮加载状态
                //清空表单
                this.adForm.On = true;
                this.adForm.SNsn = '';
                this.adForm.IMEI = '';
                this.adForm.classification = '';
                this.adForm.notes = '';
                this.adForm.firstDetectionTime = '';
                this.adForm.timeInterval = '';
            }, 1000);

            console.log(this.adForm);
            const vm = this; // 引用 Vue 组件实例
        },
        delDevice(devicex) {
            console.log(devicex);
            const vm = this; // 引用 Vue 组件实例
            this.$confirm('此操作将永久删除该设备信息以及关联的数据, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.$axios.delete(`http://119.91.64.37/PHP/delDevice.php?sn_device=${devicex.sn_number}`)
                    .then(function (response) {
                        console.log(response);
                        if (response.data.success) {
                            vm.$message({
                                type: 'success',
                                message: '删除设备成功!'
                            });
                        } else {
                            // console.error('删除设备失败');
                            vm.$message({
                                type: 'error',
                                message: '删除设备失败!'
                            });
                        }
                        // 隐藏加载状态
                        // vm.devLoading = false;
                        // 刷新表格
                        vm.$nextTick(() => {
                            vm.$refs.deviceTable.doLayout();
                        });
                    }).catch(function (error) {
                        console.log(error);
                        console.error('删除设备失败!');
                    });

                setTimeout(() => {
                    this.searchDevice();//重新加载设备列表
                    this.devLoading = false;
                }, 1000);

            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除!'
                });
            });
        },
        editDevice(forname) {
            this.editdialogFormVisible = true//可见弹窗
            // console.log(forname);
            this.form.SNsn = forname.sn_number;
            this.form.IMEI = forname.imei_number;
            this.form.timeInterval = forname.timeInterval;
            // let parts = forname.firstDetectionTime.match(/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/);
            // if (parts) {
            //     // 使用分组匹配的值构建ISO 8601格式的日期字符串
            //     this.form.firstDetectionTime = new Date(
            //         parseInt(parts[1], 10),
            //         parseInt(parts[2], 10) - 1, // 月份需要减1
            //         parseInt(parts[3], 10),
            //         parseInt(parts[4], 10),
            //         parseInt(parts[5], 10),
            //         parseInt(parts[6], 10)
            //     ).toISOString();
            // }
            // console.log(forname.sn_number);
        },
        saveDevice() {
            this.editConfirm = true;
            // 发送表单数据到后端
            this.$axios.post('http://119.91.64.37/PHP/editDevice.php', JSON.stringify(this.form))
                .then(response => {
                    // 处理成功响应
                    console.log('编辑成功!', response);
                    // this.editdialogFormVisible = false; // 关闭编辑弹窗
                    // 可以刷新设备列表或更新设备数据
                    // ...
                    this.$message({
                        type: 'success',
                        message: '编辑设备成功!'
                    });
                })
                .catch(error => {
                    // 处理错误响应
                    console.error('编辑失败!', error);
                    // 可以显示错误消息
                    // ...
                    this.$message({
                        type: 'error',
                        message: '设备编辑失败!'
                    });
                });

            setTimeout(() => {
                this.searchDevice();//重新加载设备列表
                this.editdialogFormVisible = false; // 关闭编辑弹窗
                //清空表单
                this.form.On = true;
                this.form.SNsn = '';
                this.form.IMEI = '';
                this.form.classification = '';
                this.form.notes = '';
                this.form.firstDetectionTime = '';
                this.form.timeInterval = '';
            }, 1000);

            this.editConfirm = false;
            // console.log(this.form.SNsn);

            // this.editdialogFormVisible = false//不可见弹窗

        },
        setCurrent(row) {
            this.$refs.singleTable.setCurrentRow(row);
        },
        handleCurrentChange(val) {
            this.currentRow = val;
        },
    },
    mounted() {
        setTimeout(() => {
            // 在Vue组件中使用Axios来获取设备列表
            this.$axios.get('http://119.91.64.37/PHP/searchDeviceAll.php')
                .then((response) => {
                    this.devices = response.data; // 将获取到的数据赋值给devices属性
                    this.devLoading = false; // 隐藏加载状态
                })
                .catch((error) => {
                    console.log(error);
                    this.devLoading = false; // 在出现错误时也隐藏加载状态
                });
        }, 100);
    },
};
</script>
<style lang="less" scoped>
.el-row {
    margin-bottom: 20px;

    &:last-child {
        margin-bottom: 0;
    }
}

.el-col {
    border-radius: 4px;
}

.bg-purple-dark {
    background: #153158;
}

.grid-content {
    border-radius: 5px;
    min-height: 50px;
}
</style>