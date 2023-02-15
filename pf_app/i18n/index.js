import { createI18n } from "vue-i18n";
import lang from "./lang/index";

export const i18nOpt = {
  locale: uni.getStorageSync('locale') || 'zh-cn',
  messages: lang
}

export const setupI18n = (app) => {
  const i18n = createI18n(i18nOpt);
  app.use(i18n);
}
