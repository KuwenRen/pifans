
class Soc {
	constructor(arg) {
		this.socketTask = {}
		this.login = null
		this.timeId = 0
		this.callBack = {}
	}
	
	connect(){
		this.socketTask = uni.connectSocket({
			url: "wss://www.pifan.club:7272", //仅为示例，并非真实接口地址。
			complete: ()=> {}
		});
		if(this.socketTask.onOpen){
			this.onEvent()
		}else{
			console.log("失败")
		}
		
	}
	
	
	onMsg(name,callBack,isRouter=true){
		this.callBack[name] = {
			fn:callBack,
			isRouter
		}
	}
	
	closeMsg(name){
		delete this.callBack[name]
	}
	
	// 发送token和key 每30秒一次
	sendToken(){
		clearInterval(this.timeId)
		this.timeId = setInterval(()=>{
			this.send({
				m: "",
				c: "",
				a: "",
				c_type:"check",
			},()=>{
				this.connect()
			})
		},10000)
	}
	
	close(){
		if(this.socketTask.close){
			this.socketTask.close()
			clearInterval(this.timeId)
			console.log("断开连接")
		}
	}
	
	send(data,fail=function(){
		console.log("发送失败")
	},){
		this.login = uni.getStorageSync("login")
		if(!this.login) return
		let sendData = {
				...data,
				header: {
					"api-login-key": this.login.token_key,
					"api-login-token": this.login.token
				}
		}
		this.socketTask.send({
			data:JSON.stringify(sendData),
			fail:fail
		})
	}
	
	// 注册事件监听
	onEvent(){
		
		this.socketTask.onOpen(()=>{
			this._onMsg()
			this.send({
				m: "",
				c: "",
				a: "",
				c_type:"check",
			})
		})
		this.socketTask.onClose(()=>{
			// console.log("链接已关闭")
		})
		this.socketTask.onError(()=>{
			// console.log("链接错误")
		})
	}
	_onMsg(){
		this.socketTask.onMessage(({data})=>{
			data = JSON.parse(data)
			if(data.code==1000){
				uni.setStorageSync("login",'')
				return
			}
			if(data.code==-1){
				uni.showToast({
					icon:"none",
					title:data.msg
				})
			}
			for (let key in this.callBack) {
				if(this.callBack[key].isRouter){
					if(data.request&&this.callBack[key]){
						let flag = true
						if(!api[key]){
							flag = false
						}else{
							for (let key2 in data.request) {
								if(data.request[key2] != api[key][key2]){
									flag = false
								}
							}
						}
						if(flag){
							this.callBack[key].fn(data)
						}
					}
				}else{
					this.callBack[key].fn(data)
				}
				
				
			}
		})
	}
}

export default Soc;