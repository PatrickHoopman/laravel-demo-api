import axios from 'axios';
import { boot } from 'quasar/wrappers';

export default boot(({ Vue }) => {
  const api = axios.create({ baseURL: `http://${document.location.hostname}:8081/api` })
  Vue.prototype.$api = api
});