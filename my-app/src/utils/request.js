import axios from 'axios'

const http = axios.create({
    baseURL: '119.91.64.37/PHP',//请求基地址
    timeout: 10000,//超时时间
})

export default http