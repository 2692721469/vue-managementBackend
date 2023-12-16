<template>
    <el-row style="display: space-between; justify-content: space-between;">
        <el-col :span="14" style="padding-right: 20px;">
            <el-card class="box-card">
                <div slot="header" style="text-align: center;">
                    <span>设备位置信息概览</span>
                </div>
                <div style="height: 80vh;">
                    <baidu-map class="map" @ready="BMapReady" :center="center" :zoom="zoom"
                        :scroll-wheel-zoom="scroll_wheel_zoom">
                        <bm-map-type :map-types="['BMAP_NORMAL_MAP', 'BMAP_HYBRID_MAP']"
                            anchor="BMAP_ANCHOR_TOP_LEFT"></bm-map-type>
                        <!-- <bm-marker :position="{lng: 116.404, lat: 39.915}" :dragging="true" animation="BMAP_ANIMATION_BOUNCE"></bm-marker> -->
                        <bm-marker v-for="position in positionData" :key="position.sn_number"
                            :position="{ lng: position.lng, lat: position.lat }" @click="positionMark(position)">
                            <bm-label :content="position.sn_number"
                                :labelStyle="{ border: '1px solid black', padding: '5px', color: 'black', fontSize: '15px' }"
                                :offset="{ width: 30, height: 0 }" />
                        </bm-marker>
                    </baidu-map>
                </div>
            </el-card>
        </el-col>

        <el-col :span="10">
            <div class="dev-info" ref="tableContainer">
                <el-card :body-style="{ display: 'flex' }" shadow="hover">
                    <i class="icon el-icon-success" :style="{ background: '#00e500' }" @click="getOnlineDevice()"></i>
                    <div class="detail" @click="getOnlineDevice()">
                        <p class="number">{{ countData[0].value }}</p>
                        <!-- <p class="number">1</p> -->
                        <p class="name">设备在线数</p>
                    </div>
                </el-card>

                <el-dialog title="在线设备列表" :visible.sync="onlineTableVisible">
                    <el-table :data="onlineDeviceData" stripe style="width: 100%" :max-height="tableHeight">
                        <el-table-column prop="SNsn" label="设备SN编号" width="200">
                        </el-table-column>
                        <el-table-column prop="IMEI" label="设备IMEI编号" width="200">
                        </el-table-column>
                        <el-table-column prop="classification" label="项目" width="200">
                        </el-table-column>
                        <el-table-column prop="whetherOn" label="是否启用设备">
                        </el-table-column>
                    </el-table>
                </el-dialog>

                <el-card :body-style="{ display: 'flex' }" shadow="hover">
                    <i class="icon el-icon-warning" :style="{ background: '#ffa500' }" @click="getOfflineDevice()"></i>
                    <div class="detail" @click="getOfflineDevice()">
                        <p class="number">{{ countData[1].value }}</p>
                        <!-- <p class="number">1</p> -->
                        <p class="name">设备离线数</p>
                    </div>
                </el-card>

                <el-dialog title="离线设备列表" :visible.sync="offlineTableVisible">
                    <el-table :data="offlineDeviceData" stripe style="width: 100%" :max-height="tableHeight">
                        <el-table-column prop="SNsn" label="设备SN编号" width="200">
                        </el-table-column>
                        <el-table-column prop="IMEI" label="设备IMEI编号" width="200">
                        </el-table-column>
                        <el-table-column prop="classification" label="项目" width="200">
                        </el-table-column>
                        <el-table-column prop="whetherOn" label="是否启用设备">
                        </el-table-column>
                    </el-table>
                </el-dialog>

                <el-card :body-style="{ display: 'flex' }" shadow="hover">
                    <i class="icon el-icon-question" :style="{ background: '#2ec7c9' }"></i>
                    <div class="detail">
                        <p class="number">{{ countData[2].value }}</p>
                        <!-- <p class="number">1</p> -->
                        <p class="name">上报事件数</p>
                    </div>
                </el-card>

                <el-card :body-style="{ display: 'flex' }" shadow="hover">
                    <i class="icon el-icon-error" :style="{ background: '#ff0000' }" @click="getErrorInfoData()"></i>
                    <div class="detail" @click="getErrorInfoData()">
                        <p class="number">{{ countData[3].value }}</p>
                        <!-- <p class="number">1</p> -->
                        <p class="name">故障信息数</p>
                    </div>
                </el-card>

                <el-dialog title="故障信息列表" :visible.sync="errorInfoTableVisible">
                    <el-table :data="errorInfoData" stripe style="width: 100%" :max-height="tableHeight">
                        <el-table-column prop="sn_number" label="设备SN编号" width="200">
                        </el-table-column>
                        <el-table-column prop="imei_number" label="设备IMEI编号" width="200">
                        </el-table-column>
                        <el-table-column prop="category" label="设备分类" width="200">
                        </el-table-column>
                        <el-table-column prop="message" label="故障信息" width="200">
                        </el-table-column>
                        <el-table-column prop="date" label="故障信息上报时间">
                        </el-table-column>
                    </el-table>
                </el-dialog>
            </div>

            <div class="info-frame">
                <el-card>
                    <div slot="header" style="text-align: center;">
                        <span style="color: black; font-size: 20px;">信息栏</span>
                    </div>
                    <el-table :data="tableData" stripe style="width: 100%" :max-height="tableHeight">
                        <el-table-column prop="sn_number" label="设备SN编号" width="140">
                        </el-table-column>
                        <el-table-column prop="category" label="项目" width="90">
                        </el-table-column>
                        <el-table-column prop="message" label="上报信息" width="200">
                        </el-table-column>
                        <el-table-column prop="date" label="上报时间">
                        </el-table-column>
                    </el-table>
                </el-card>
            </div>

        </el-col>

    </el-row>
</template>

<script>
export default {
    data() {
        return {
            onlineTableVisible: false,
            offlineTableVisible: false,
            errorInfoTableVisible: false,
            tableHeight: 420,
            countData: [],//状态栏数据
            tableData: [],//信息栏数据
            onlineDeviceData: [],//在线设备点击弹窗显示内容
            offlineDeviceData: [],//离线设备点击弹窗显示内容
            errorInfoData: [],//故障信息点击弹窗显示内容

            /////////map//////////////////////
            positionData: [{}],//位置信息数据
            detectionData: [{}],//展开大图表下面的详细数据
            chartDataList: [{}],//侧边小图表数据
            center: { lng: 0, lat: 0 },
            zoom: 3,//默认缩放
            scroll_wheel_zoom: true,//鼠标滚轮缩放地图
        };
    },
    methods: {
        //////////////////////////////////////////////////////////////////地图显示////////////////////////////////////////////////
        async fetchAllData() {
            try {
                const response = await this.$axios.get('http://119.91.64.37/PHP/getAllData.php');
                this.positionData = response.data.positionData;
                this.detectionData = response.data.detectionData;
                this.chartDataList = response.data.chartDataList;
                console.log("GET All Data.");
                // console.log(this.chartDataList);
                return response; // 返回axios.get的Promise
            } catch (error) {
                console.error('API请求出错', error);
                throw error; // 抛出错误以便上层捕获
            }
        },
        async BMapReady({ BMap, map }) {
            await this.fetchAllData();
            console.log(BMap, map);
            console.log("百度地图就绪");

            // GPS坐标转为百度坐标
            await this.convertCoordinates();

            // 计算所有点的平均中心
            let totalLng = 0;
            let totalLat = 0;
            this.positionData.forEach((item) => {
                totalLng += parseFloat(item.lng);
                totalLat += parseFloat(item.lat);
            });

            const avgLng = totalLng / this.positionData.length;
            const avgLat = totalLat / this.positionData.length;

            this.center.lng = avgLng;
            this.center.lat = avgLat;
            this.zoom = 11;
        },
        async convertCoordinates() {
            console.log("进行坐标转换");
            await Promise.all(this.positionData.map(item => this.wgs84ToBd09(item.lng, item.lat)))
                .then(results => {
                    results.forEach((result, index) => {
                        if (result.status === 0) {
                            this.positionData[index].lng = result.points[0].lng.toString();
                            this.positionData[index].lat = result.points[0].lat.toString();
                        } else {
                            console.error(`坐标转换失败：${result.message}`);
                        }
                    });
                });
        },
        async wgs84ToBd09(lng, lat) {
            console.log("wgs84ToBd09坐标转换");
            const convertor = new BMap.Convertor();
            const point = new BMap.Point(parseFloat(lng), parseFloat(lat));
            const points = [point];

            return new Promise(resolve => {
                convertor.translate(points, 1, 5, result => {
                    resolve(result);
                });
            });
        },
        positionMark(position) {
            if (position) {
                console.log("点击标注点事件");
            }
        },
        //////////////////////////////////////////////////////////////////END////////////////////////////////////////////////
        init({ BMap, map }) {
            //enableMapClick:false 表示禁止地图内景点信息点击
            map = new BMap.Map('baiduMap', { enableMapClick: false })
            // 设置地图允许的最大最小级别
            map.setMinZoom(5)
            map.setMaxZoom(20)
            // 开启鼠标滚轮缩放
            map.enableScrollWheelZoom(true)
            // 设置中心点坐标和地图级别
            map.centerAndZoom(this.city, 14)
            // 创建自定义标记 参数:自定义图片路径 大小 偏移量
            const icon = new BMap.Icon(
                // require('./assets/image/youtong-icon-01.png'),
                new BMap.Size(20, 30),
                { anchor: new BMap.Size(0, 0) }
            )
            // 根据坐标批量生成自定义图标点
            this.cityData.forEach((item) => {
                // 创建点
                const point = new BMap.Point(item.enti_lnt, item.enti_lat)
                // 创建标注
                const marker = new BMap.Marker(point, { icon: icon })
                // 将标注添加到地图中
                map.addOverlay(marker)
                // 给标记点添加点击事件
                marker.addEventListener('click', (e) => {
                    执行想进行的操作(经个人测试在此处注册点击事件效果最佳, 该有的数据项都可以获取)
                })
            })
        },
        getOnlineDevice() {
            this.onlineTableVisible = true;//显示对话框
        },
        getOfflineDevice() {
            this.offlineTableVisible = true;//显示对话框
        },
        getErrorInfoData() {
            this.errorInfoTableVisible = true;//显示对话框
        },
        fetchDataFromBackend() {
            this.$axios.get('http://119.91.64.37/PHP/getHomeData.php')
                .then(response => {
                    this.countData = response.data.countData; // 更新 countData 数据
                    this.tableData = response.data.tableData;
                    this.onlineDeviceData = response.data.onlineDeviceData;
                    this.offlineDeviceData = response.data.offlineDeviceData;
                    this.errorInfoData = response.data.errorInfoData;
                    // console.log(this.countData);
                    // console.log(this.tableData);
                })
                .catch(error => {
                    console.error('API请求出错', error);
                });
        },
    },
    created() {
        this.fetchDataFromBackend();
        // this.fetchAllData();
    },
};

</script>

<style lang="less" scoped>
.dev-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;

    .icon {
        width: 80px;
        height: 80px;
        font-size: 30px;
        text-align: center;
        line-height: 80px;
        color: #fff;
    }

    .detail {
        display: flex;
        flex-direction: column;
        justify-content: center;

        p {
            margin: 0;
        }

        .number {
            padding-left: 15px;
            font-size: 30px;
        }

        .name {
            padding-left: 15px;
            font-size: 15px;
            color: #7c7c7c;
        }
    }

    .el-card {
        width: 48%;
        margin-bottom: 20px;
    }
}

.map {
    width: 100%;
    height: 100%;
}
</style>