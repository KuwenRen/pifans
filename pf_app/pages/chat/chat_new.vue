<!-- 
@author x
@title 群聊天信息
 -->
<template>
	<view class="page2">
		<u-navbar :showMore="true" :title="name=='English'?'English Chat':$t(name+'('+roomUserCount+')')"
			bgColor="#14142B" leftIconColor="#F585FF" autoBack @moreClick="moreClick">
		</u-navbar>
		<view class="mycontent">
			<view :class="['content-wapper',!showTost?'hideContent':'showContent']">
				<view class="msg-wapper">
					<msg v-for="(item,index) in copyList.slice(0,15)" :key="index" :data="item"></msg>
				</view>
			</view>

			<scroll-view @scrolltoupper="scrolltoupper" :class="['content-wapper',showTost?'hideContent':'showContent']"
				:scroll-into-view="scrollId" scroll-y="true" @click="tabContent" :scroll-top="scrollTop">
				<view class="msg-wapper">
					<msg @tabImg="tabImg(item.content)" :id="'scrollId'+item.id" v-for="(item,index) in msgList"
						:key="index" :data="item"></msg>
				</view>
			</scroll-view>
		</view>
		<view class="bottom">
			<view class="input-wapper">
				<view class="input-box">
					<textarea placeholder-style="color:#FFF" v-model="text" :adjust-position="true" class="input"
						:auto-height="true" type="text" value="" :placeholder="$t('输入新消息')" />
				</view>
				<text :class="['iconfont icon-icon_sent',text.trim()?'icon-icon_sent-ac':'']"
					@touchend.prevent="tabSend">
				</text>
			</view>
			<!-- <view class="btn-wapper">
				<text @click="changeImage('album')" :class="['iconfont icon-tupian1',bottomIndex==1?'bottom-ac':'']"></text>
				<text @click="changeImage('camera')" :class="['iconfont icon-xiangji',bottomIndex==2?'bottom-ac':'']"></text>
			</view> -->

			<!-- 底部弹出内容 -->
			<!-- <view class="bottom-content-wapper" v-show="bottomIndex!=null" :style="{height:keyHeight+'px'}">
				<swiper v-if="bottomIndex==0" class="swiper" :indicator-dots="true" :autoplay="false" indicator-color="#F2F2F2" indicator-active-color="#FF8023">
					<swiper-item v-for="(list,index) in expression" :key="index">
						<view class="swiper-item">
							<view class="ex-item" @click="addEx(item)" v-for="(item,index) in list" :key="index">{{item}}</view>
						</view>
					</swiper-item>
				</swiper>
			</view> -->
		</view>
	</view>
</template>

<script>
	import Soc from '@/api/socket.js'
	import msg from './msg1.vue'
	const expression = ["😖", "😠", "😩", "😲", "😞", "😵", "😰", "😒", "😍", "😤", "😜", "😝", "😋", "😘", "😚", "😷",
		"😳", "😃", "😆", "😁", "😂", "😄", "😢", "😭", "😨", "😣", "😡", "😌", "😔", "😱", "😪", "😏", "😓", "😥",
		"😫", "😉", "😺", "😸", "😹", "😽", "😻", "😿", "😼", "🙀", "🐎", "🐔", "🐗", "🐫", "🐘", "🐨", "🐒", "🐑",
		"🐙", "🐚", "🌴", "🌵"
	];
	import {
		gatewayserver_bind,
		gatewayserver_send,
		getNewChatRecordList
	} from '../../api/index.js'
	export default {
		data() {
			return {
				name: "",
				user: {},
				isInit: false,
				id: "",
				type: "",
				isLoading: false,
				page: 1,
				bottomIndex: null,
				scrollTop: 500000,
				keyHeight: 0,
				text: "",
				expression: [],
				expression2: [...expression],
				msgList: [],
				copyList: [],
				scrollId: "",
				showTost: false,
				service: {},
				timeId: 0,
				roomUserCount: 0,
				timer: null,
				webSocket: null
			}
		},
		onLoad({
			id,
			roomId,
			type,
			name,
			count
		}) {
			this.name = name
			this.id = roomId ? roomId : id
			this.type = type
			this.roomUserCount = count
			this.getUser()
			this.connectSocket()
		},
		async onReady() {
			await this.getRecord()
			this.copyList = [...this.msgList]
			this.scrollBottom()
		},
		onUnload() {
			uni.closeSocket()
			clearInterval(this.timer)
		},
		onShow() {
			let group_name = uni.getStorageSync('group_name');
			if(group_name){
				this.name = group_name
			}
		},
		components: {
			msg
		},
		watch: {
			bottomIndex() {
				this.scrollBottom()
			},
			text() {
				if (this.text != "") {
					this.scrollBottom()
				}
			},
			msgList() {
				this.msgList = this.initTime(this.msgList)
			},
		},
		methods: {

			joinGroup() {
				let data = JSON.stringify({
					"chat_room_id": this.id,
					"uid": this.$store.state.userInfo.id,
					"type": "join"
				})
				console.log('加入聊天室')
				this.heart() //心跳
				uni.sendSocketMessage({
					data: data
				})
			},
			heart() {
				let that = this;
				clearInterval(this.timer);
				this.timer = '';
				console.log('心跳')
				let data = JSON.stringify({
					"chat_room_id": this.id,
					"uid": this.$store.state.userInfo.id,
					"type": "ping"
				})
				this.timer = setInterval(() => {
					uni.sendSocketMessage({
						data: data
					})
				}, 300000)//5分钟心跳一次

			},
			connectSocket() {
				uni.connectSocket({
					url: "ws://chat.pifans.app",
					success(res) {
						console.log("连接成功");
					},
					fail(err) {
						console.log("报错", err);
					}
				});
				uni.onSocketOpen(() => {
					console.log('WebSocket连接已打开！');
					this.joinGroup() //加入聊天室
				})
				uni.onSocketError(function(res) {
					console.log('WebSocket连接打开失败，请检查！');
				});
				uni.onSocketMessage(resp => {
					//接收消息
					this.onMsg(JSON.parse(resp.data))
				})

			},
			/* 更多 */
			moreClick() {
				uni.navigateTo({
					url: `/pages/chat/chat-group-info?roomId=${this.id}`
				})
			},

			async bindId(client_id) {
				gatewayserver_bind({
					room_id: this.id,
					client_id
				})
			},

			async getUser() {
				this.user = uni.getStorageSync("userInfo")
				this.user = JSON.parse(this.user)
			},

			// 预览图片
			tabImg(url) {
				let imgList = this.msgList.filter(item => item.chat_type == 2).map(item => item.content)
				let cur;
				imgList.forEach((item, index) => {
					if (item == url) {
						cur = index
					}
				})
				uni.previewImage({
					urls: imgList,
					current: cur
				})
			},

			// 接受消息
			// onMsg(data) {
			// 	console.log(data)
			// 	if (data.type == 'init') {
			// 		this.bindId(data.client_id)
			// 	}
			// 	if (data.user_id) {
			// 		this.msgList.push({
			// 			username: data.username,
			// 			avatar: data.avatar,
			// 			chat_type: 1,
			// 			content: data.msg,
			// 			time: new Date().getTime() / 1000,
			// 			location: data.user_id != this.user.id ? 'left' : 'right'
			// 		})
			// 		this.scrollBottom()
			// 	}
			// },
			onMsg(msgData) {
				console.log(JSON.stringify(msgData))
				if (msgData.type == 'init') {
					this.bindId(data.client_id)
				}
				if (msgData.code == 1) {
					let data = msgData.data
					this.msgList.push({
						username: data.nickname,
						avatar: data.avatar,
						chat_type: 1,
						content: data.content,
						time: new Date().getTime() / 1000,
						location: data.user_id != this.user.id ? 'left' : 'right'
					})
					this.scrollBottom()
				} else if (msgData.code == 0) {
					uni.showModal({
						title: "消息提示",
						content: msgData.message,
						showCancel: false
					})
				}
			},

			closeMsg() {
				this.socket.closeMsg("msg")
			},

			// 点击发送按钮
			tabSend(e) {
				if (this.text.trim()) {
					this.sendMsg({
						content: this.text
					})
					this.text = ""
				}
			},

			// 发送消息
			async sendMsg({
				content,
				type = 1
			}) {
				this.msgList.push({
					avatar: this.user.avatar,
					chat_type: type,
					content,
					time: new Date().getTime() / 1000,
					location: "right"
				})
				let data = JSON.stringify({
					"chat_room_id": this.id,
					"uid": this.$store.state.userInfo.id,
					"type": "send",
					"message": content
				})
				uni.sendSocketMessage({
					data: data
				})
				this.$forceUpdate()
				this.scrollBottom()
			},

			// 发送客服消息
			async sendServiceMsg(content, type) {
				let res = await this.ajax({
					url: "service_send",
					data: {
						send_msg: content,
						send_type: type
					}
				})
			},

			// 获取历史消息
			async getRecord(init) {
				let data = {
					'chat_room_id': this.id,
					'limit': 20,
					'page': this.page
				}
				let that = this
				uni.request({
					url: 'https://chat.pifans.app/v1/chatroom/get/chatTalking',
					data: data,
					success: (res) => {
						if (res.data.code == 0) {
							let result = res.data.data
							if (result) {
								let arr = result.list.map(item => {
									return {
										username: item.nickname,
										avatar: item.avatar,
										chat_type: 1,
										content: item.content,
										id: item.id,
										time: item.createtime,
										location: item.user_id != that.user.id ? 'left' : 'right'
									}
								}).reverse() //数组反转
								that.msgList = arr.concat(that.msgList)
								uni.hideLoading();
								that.$forceUpdate()
							}

						}
					}
				})
				// let res = await getNewChatRecordList({
				// 	limit: 40,
				// 	chat_room_id: this.id,
				// 	page: 1,
				// 	// id: this.msgList[0] ? this.msgList[0].id : ''
				// })

				return false;
				if (res[1]['data'].code == 1) {
					// res.data.forEach(item=>{
					// 	if(item.send_msg){
					// 		item.content = item.send_msg
					// 		item.chat_type = item.send_type
					// 	}
					// 	if(item.location=='left'){
					// 		item.avatar = require("@/static/img/kefuAvatar.jpeg")
					// 	}
					// })

					let arr = result.list.map(item => {
						return {
							username: item.username,
							avatar: item.avatar,
							chat_type: 1,
							content: item.msg,
							id: item.id,
							time: item.createtime,
							location: item.user_id != this.user.id ? 'left' : 'right'
						}
					}).reverse()
					this.msgList = arr.concat(this.msgList)
					this.$forceUpdate()
				}
			},

			initTime(list) {
				let time = 0;
				list.forEach(item => {
					if ((item.time - time) > 5 * 60) {
						item.isTime = true
					} else {
						item.isTime = false
					}
					time = item.time
				})
				return list
			},


			async scrolltoupper() {
				if (this.page == -1) return
				// if (this.type == 3) return
				this.page++;
				this.isLoading = true
				uni.showLoading({})
				let id = "scrollId" + this.msgList[0].id
				this.showTost = true
				await this.getRecord()
				setTimeout(() => {
					this.scrollId = id
					setTimeout(() => {
						this.isLoading = false
						this.showTost = false
						this.copyList = [...this.msgList]
					}, 50)
				}, 400)

			},
			addEx(item) {
				this.text += item
			},
			// 选择图片
			changeImage(type) {
				let _this = this
				uni.chooseImage({
					count: 1,
					sourceType: [type],
					success(res) {
						res.tempFilePaths.forEach(async item => {
							console.log(item)
							uni.showLoading()
							let data = await _this.ajax({
								url: "uploadsImg",
								type: "upload",
								filePath: item
							})
							uni.hideLoading()
							console.log(data)
							if (data.code == 200) {
								_this.sendMsg({
									content: data.data.url,
									type: 2
								})
							}
						})
					}
				})
			},

			// 初始化界面
			initPage() {
				let keyHeight = uni.getStorageSync("keyHeight");
				this.keyHeight = keyHeight ? keyHeight : 0
				// 每行排版7个
				let num = 7 * parseInt(this.keyHeight / 51);
				while (this.expression2.length > num) {
					this.expression.push(this.expression2.splice(0, num))
				}
				if (this.expression2.length) {
					this.expression.push(this.expression2)
				}
				uni.onKeyboardHeightChange(({
					height
				}) => {
					if (height) {
						this.bottomIndex = 5
					} else if (this.bottomIndex == 5) {
						this.bottomIndex = null
					}
				})
			},
			tabContent() {
				this.bottomIndex = null
			},
			scrollBottom() {
				this.$nextTick(() => {
					this.scrollTop++
					setTimeout(() => {
						this.scrollTop++
					}, 500)
				})
			}
		}
	}
</script>

<style>
	.page2 {
		height: 100%;
		display: flex;
		flex-direction: column;
		height: 100vh;
	}

	.msg-wapper {
		padding: 0 30rpx 20rpx;
	}

	.mycontent {
		flex: 1;
		overflow: hidden;
		box-sizing: border-box;
		/* background-color: red; */
		position: relative;
	}

	.content-wapper {
		position: absolute;
		width: 100%;
		height: 100%;
		left: 0;
		top: 0;
	}

	.bottom {
		background-color: rgb(20, 20, 43);
		box-shadow: 0 0 6rpx rgba(255, 255, 255, 6);
		/* padding: 30rpx; */
	}

	.input {
		font-size: 30rpx;
		padding-bottom: 0;
		overflow: hidden;
		width: 100%;
	}

	.input-box {
		background-color: rgb(50, 50, 86);
		flex: 1;
		padding: 16rpx 20rpx;
		border-radius: 36rpx;
	}

	.input-wapper {
		display: flex;
		padding: 20rpx 30rpx;
		align-items: center;
	}

	.btn-wapper {
		display: flex;
		align-items: center;
		border-bottom: 1px solid #F2F2F2;
		position: relative;
		z-index: 2;
	}

	.iconfont {
		font-size: 50rpx;
		color: #666;
		width: 100rpx;
		text-align: center;
		box-sizing: border-box;
		padding-bottom: 20rpx;
		padding-top: 20rpx;
		display: block;
		border-bottom: 3px solid rgba(0, 0, 0, 0);
	}

	.icon-icon_sent-wapper {
		flex: 1;
		display: flex;
		justify-content: flex-end;
	}

	.icon-icon_sent {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		line-height: 80rpx;
		font-size: 40rpx;
		padding: 0;
		background-color: #E8E8E8;
		margin-left: 30rpx;
	}

	.icon-icon_sent-ac {
		color: #FFF;
		background-color: #FF8023;
		transition: all .5s;
	}

	.bottom-content-wapper {
		height: 470rpx;
	}

	.swiper {
		height: 100%;
		width: 100%;
	}

	.swiper-item {
		display: flex;
		flex-wrap: wrap;
	}

	.ex-item {
		width: 107rpx;
		height: 50px;
		text-align: center;
		line-height: 107rpx;
		font-size: 40rpx;
	}

	.hideContent {
		z-index: 0;
		opacity: 0;
	}

	.showContent {
		z-index: 3;
	}

	.loding {
		left: 50%;
		transform: translateX(50%);
		position: absolute;
	}

	.loading-wapper {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 30rpx;
		position: fixed;
		width: 750rpx;
		z-index: 99;
	}
</style>
