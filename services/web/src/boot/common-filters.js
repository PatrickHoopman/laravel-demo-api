import axios from 'axios';
import { boot } from 'quasar/wrappers';

const commonFiltersPlugin = {
  install(Vue) {
    Vue.mixin({
      filters: {
        monetize: function (value) {
          if (parseFloat(value) !== NaN) {
            return parseFloat(value).toFixed(2)
          }
        }
      },
    })
  }
}

export default boot(({ Vue }) => {
  Vue.use(commonFiltersPlugin)
});