import axios from 'axios';

import { userStore } from 'src/stores/auth';
// const HOST = 'http://localhost/';
// const BASE_URL = "`HOST`/api/";
const BASE_URL = 'http://localhost/';

const api = axios.create({
  baseURL: BASE_URL,
  withCredentials: false,
});

api.defaults.headers.common['Content-Type'] = 'application/json';
api.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
const store = userStore();
api.interceptors.request.use(
  (config) => {
    // Do something before request is sent
    config.headers['Authorization'] = 'Bearer ' + store.getToken;
    return config;
  },
  (error) => {
    Promise.reject(error);
  }
);

export default api;
