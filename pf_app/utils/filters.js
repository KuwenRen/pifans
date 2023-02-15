export function timeInit(time,type="Y-M-D h:m:s"){
	if(!time) return ""
	const date = new Date(isNaN(time*1000)?time:time*1000) //年
	let Y = date.getFullYear().toString() //月份
	let M = (date.getMonth() + 1).toString() //月份   
	let D = date.getDate().toString() //日   
	let h = date.getHours().toString() //小时   
	let m = date.getMinutes().toString() //分   
	let s = date.getSeconds().toString() //秒
	
	
	
	
	M=M.length==1?"0"+M:M
	D=D.length==1?"0"+D:D
	h=h.length==1?"0"+h:h
	m=m.length==1?"0"+m:m
	s=s.length==1?"0"+s:s
	
	if(type=='msg'){
		const date2 = new Date() //年
		let Y2 = date.getFullYear().toString() //月份
		let M2 = (date.getMonth() + 1).toString() //月份   
		let D2 = date.getDate().toString() //日   
		let h2 = date.getHours().toString() //小时   
		let m2 = date.getMinutes().toString() //分   
		let s2 = date.getSeconds().toString() //秒
		M2=M2.length==1?"0"+M2:M2
		D2=D2.length==1?"0"+D2:D2
		h2=h2.length==1?"0"+h2:h2
		m2=m2.length==1?"0"+m2:m2
		s2=s2.length==1?"0"+s2:s2
		// return "今天123456"
		if(Y2 == Y && M2==M ){
			if(D2==D){
				return `${h}:${m}`
			}else if(Math.abs(parseInt(D2)-parseInt(D))==1){
				return `昨天 ${h}:${m}`
			}else if(Math.abs(parseInt(D2)-parseInt(D))==2){
				return `前天 ${h}:${m}`
			}else{
			}
		}
	}
	
	type = type.replace("Y",Y).replace("M",M).replace("D",D).replace("h",h).replace("m",m).replace("s",s)
	return type;
}

export function phoneInit(phone){
	return phone.slice(0,3)+"****"+phone.slice(7)
}
export function sexInit(sex){
	let arr = ["保密","男","女"]
	return arr[sex];
}

export function countDown(endTime,name){
	const curTime = new Date().getTime()
	const timeDiff = parseInt((endTime*1000 - curTime)/1000) + 10
	if(name == "hour"){
		return parseInt(timeDiff / 3600) 
	}else if(name=="minute"){
		return parseInt(timeDiff / 60) % 60
	}else if(name == "second"){
		return timeDiff % 60 
	}else{
		return timeDiff
	}
}