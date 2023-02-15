export const formatTime = (time, cFormat) => {
  if (arguments.length === 0) {
    return null
  }
  if (time == null || time == '') {
    return ''
  }
  var format = cFormat || '{y}-{m}-{d}'
  var date
  if (typeof time === 'object') {
    date = time
  } else {
    time = typeof time === 'string'?parseInt(time):time;
    date = new Date(time)
  }
  var formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  }
  var reg = new RegExp("{(y|m|d|h|i|s|a)+}", "g");
  var time_str = format.replace(reg, function (result, key) {
    var value = formatObj[key]
    if (key === 'a') {
      return ['日', '一', '二', '三', '四', '五', '六'][value]
    }
    if (result.length > 0 && value < 10) {
      value = '0' + value
    }
    return value || 0
  })
  return time_str
}
