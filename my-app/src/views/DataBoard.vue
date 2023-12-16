<template>
    <el-row style="display: space-between; justify-content: space-between;">
        <el-col :span="14" style="padding-right: 20px;">
            <el-card class="box-card">
                <div slot="header" style="text-align: center;">
                    <span>设备位置信息概览</span>
                </div>
                <div style="height: 79vh;">
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
            <div class="scrollable-card-container">
                <el-card class="chart-card" v-for="chartData in chartDataList" :key="chartData.sn" body-style="padding: 0;"
                    shadow="hover">
                    <div slot="header" class="clearfix">
                        <span style="margin-right: 55px;">设备SN编号：{{ chartData.sn }}</span><span>设备分类：{{ chartData.category
                        }}</span>
                        <el-button style="float: right; padding: 3px 0" type="text"
                            @click="showDeviceDetails(chartData)">详细信息</el-button>
                    </div>
                    <div :id="chartData.sn" class="chart-container" style="width: 100%; height: 300px;"></div>
                </el-card>

                <!-- 使用 Dialog 组件显示设备详细检测数据 -->
                <el-dialog title="设备详细检测数据" :visible.sync="dialogVisibleBig" width="70%" top="2vh" style="padding: 0;">
                    <el-card class="chart-card" body-style="padding: 0;" shadow="hover">
                        <div slot="header" class="clearfix">
                            <span style="margin-right: 55px;">设备SN编号：{{ selectedDevice.sn }}</span><span>设备分类：{{
                                selectedDevice.category
                            }}</span>
                        </div>

                        <div id="Big" class="chart-container" style="width: 100%; height: 400px;"></div>

                        <el-table :data="detectionData" stripe style="width: 100%; padding-left: 20px; padding-right: 20px;"
                            :max-height="300">
                            <el-table-column prop="device_sn" label="设备编号" width="250">
                            </el-table-column>
                            <el-table-column prop="detection_date" label="检测时间" width="250">
                            </el-table-column>
                            <el-table-column prop="category" label="检测项目" width="250">
                            </el-table-column>
                            <el-table-column prop="detection_value" label="检测结果">
                            </el-table-column>
                        </el-table>
                    </el-card>
                </el-dialog>
            </div>
        </el-col>
    </el-row>
</template>

<script>
import * as echarts from 'echarts'
export default {
    data() {
        return {
            positionData: [{}],//位置信息数据
            detectionData: [{}],//展开大图表下面的详细数据
            chartDataList: [{}],//侧边小图表数据
            dialogVisibleBig: true, // 控制对话框的显示状态
            dialogVisible: false, // 控制对话框的显示状态
            selectedDevice: [], // 存储选定的设备数据
            // chartDataList: [
            //     { data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 1, 1, 2, 3, 4, 5, 6, 7, 8, 9], sn: 'SN123', category: 'TP' },
            //     { data: this.generateRandomData(30), sn: 'SN456', category: 'TN' },
            //     { data: this.generateRandomData(40), sn: 'SN789', category: 'S' },
            //     { data: this.generateRandomData(40), sn: 'SN700', category: 'S' },
            // ],
            center: { lng: 0, lat: 0 },
            zoom: 3,//默认缩放
            scroll_wheel_zoom: true,//鼠标滚轮缩放地图
        };
    },
    mounted() {
        // this.fetchAllData();//获取需要用到的数据。
        //在下面函数中由执行和获取数据的函数，所以上面被注释了
        this.tableInit();//获取到数据后初始化右侧的小图表
        // setTimeout(() => {
        //     this.init();
        // }, 1000); // 假设等待 1 秒后初始化

    },
    methods: {
        //////////////////////////////////////////////////////////////////地图显示////////////////////////////////////////////////
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
                console.log(position.sn_number);
                // 找到 chartDataList 中与 position.sn_number 匹配的数据
                const chartData = this.chartDataList.find(data => data.sn === position.sn_number);
                if (chartData) {
                    // 传递给图表初始化函数
                    console.log("传递参数");
                    console.log(chartData);
                    this.showDeviceDetails(chartData);
                }
            }
        },
        //////////////////////////////////////////////////////////////////END////////////////////////////////////////////////

        // generateRandomData(count) {
        //     const data = [];
        //     for (let i = 0; i < count; i++) {
        //         data.push(Math.random() * 31);
        //     }
        //     return data;
        // },
        // 处理点击事件，打开对话框
        showDeviceDetails(deviceData) {
            // console.log("click" + deviceData.sn)
            // this.selectedDevice = deviceData;
            // this.dialogVisible = true;
            console.log('Conslo');
            console.log(this.chartDataList);

            if (deviceData) {
                console.log("click" + deviceData.sn)
                this.selectedDevice = deviceData;
                this.dialogVisibleBig = true;
                console.log(this.selectedDevice)
                // 如果不存在，等待 DOM 准备好后再初始化新的图表实例
                this.$nextTick(() => {
                    // 初始化图表
                    // 销毁之前的图表实例
                    const oldChart = echarts.getInstanceByDom(document.getElementById('Big'));
                    if (oldChart) {
                        echarts.dispose(oldChart);
                    }
                    const chart = echarts.init(document.getElementById('Big'));
                    const options = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: { type: 'cross' }
                        },
                        xAxis: [
                            {
                                name: '日期',
                                type: 'category',
                                axisTick: {
                                    alignWithLabel: true
                                },
                                data: Array.from({ length: this.selectedDevice.data.length }, (_, i) => (i + 1).toString()) // 创建包含 1 到 30 的数据
                            }
                        ],
                        yAxis: {
                            type: 'value',
                            name: '日平均浓度',
                            position: 'left',
                            axisLabel: {
                                formatter: '{value} mg/L'
                            }
                        },
                        series: [
                            {
                                name: '日平均浓度',//鼠标放置显示的内容
                                type: 'scatter',
                                yAxisIndex: 0,
                                data: this.selectedDevice.data
                            },
                            // {
                            //     type: 'scatter',
                            //     data: chartData.data,
                            //     symbolSize: 10,
                            // },
                        ],
                        dataZoom: [
                            {
                                type: 'inside',
                                start: 0,
                                end: 100
                            },
                            {
                                start: 0,
                                end: 100,
                                handleIcon: 'M0,0 v9.7h5 v-9.7h-5 Z M9.7,0 v9.7h5 v-9.7h-5 Z',
                                handleSize: '100%',
                                handleStyle: {
                                    color: '#fff',
                                    shadowBlur: 3,
                                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                                    shadowOffsetX: 2,
                                    shadowOffsetY: 2
                                }
                            }
                        ],
                    };
                    chart.setOption(options);
                });
            }
        },
        async tableInit() {
            // 等待数据准备好后再初始化表格
            this.tableData = await this.fetchAllData();
            console.log("tableInit");
            this.chartDataList.forEach((chartData) => {
                const chart = echarts.init(document.getElementById(chartData.sn));
                const options = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: { type: 'cross' }
                    },
                    xAxis: [
                        {
                            name: '日期',
                            type: 'category',
                            axisTick: {
                                alignWithLabel: true
                            },
                            data: Array.from({ length: chartData.data.length }, (_, i) => (i + 1).toString()) // 创建包含 1 到 30 的数据
                        }
                    ],
                    yAxis: {
                        type: 'value',
                        name: '日平均浓度',
                        position: 'left',
                        axisLabel: {
                            formatter: '{value} mg/L'
                        }
                    },
                    series: [
                        {
                            name: '日平均浓度',//鼠标放置显示的内容
                            type: 'scatter',
                            yAxisIndex: 0,
                            data: chartData.data
                        },
                        // {
                        //     type: 'scatter',
                        //     data: chartData.data,
                        //     symbolSize: 10,
                        // },
                    ],
                };
                chart.setOption(options);
            });
        },
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
    },
    created() {
        this.dialogVisibleBig = false; // 控制对话框的显示状态
    },
};
</script>

<style lang="less" scoped>
.map {
    width: 100%;
    height: 100%;
}

.chart-card {
    // height: 300px;
    margin-bottom: 8px;
}

.scrollable-card-container {
    max-height: 89vh;
    /* 设置最大高度，超过这个高度将出现滚动条 */
    overflow-y: auto;
    /* 使用滚动条来浏览内容 */
}
</style>