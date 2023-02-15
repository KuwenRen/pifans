<template>
	<view class="container">
		<u-navbar :title="$t('Pi数据')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view style="padding-top: 160rpx;" class=""></view>
		<sy-tabs class="article-header" type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
		<view class="iframe-wapper" v-show="active==1">
			<iframe id="iframe" class="iframe" frameborder="0" src="https://dashboard.pi-blockchain.net/"></iframe>
		</view>
		<view v-if="active==2" :borderBottom="true">
			<u-form labelPosition="top" labelWidth="500" :labelStyle="{color:'#FFF'}">
				<u-form-item :label="$t('开始时间')">
					<picker mode="date" @change="changeTimeStart">
						<input style="width: 650rpx;" v-model="timeStart" class="input" :placeholder="$t('请选择具体时间')"
							disabled="true"></input>
					</picker>
				</u-form-item>
				<u-form-item :label="$t('结束时间')">
					<picker mode="date" @change="changeTime">
						<input style="width: 650rpx;" v-model="time" class="input" :placeholder="$t('请选择具体时间')"
							disabled="true"></input>
					</picker>
				</u-form-item>
				<u-form-item :label="$t('挖矿量')">
					<text class="input">{{(jcb*2).toFixed(4)}}</text>
				</u-form-item>
				<u-form-item :label="$t('基础币')">
					<text class="input">{{jcb.toFixed(4)}}</text>
				</u-form-item>
				<u-form-item :label="$t('安全圈数量')">
					<text class="input">{{jcb.toFixed(4)}}</text>
				</u-form-item>

				<!-- <u-picker mode="time" v-model="isShowTime" ></u-picker> -->
				<!-- <u-form-item label="邀请人数币">
					<text class="input">12585</text>
				</u-form-item> -->
				<!-- <u-form-item label="锁仓后的速率">
					<text class="input">12585</text>
				</u-form-item> -->
			</u-form>
		</view>
		<view v-if="active==3" :borderBottom="true">
			<u-form labelPosition="top" labelWidth="500" :labelStyle="{color:'#FFF'}">
				<u-form-item :label="$t('价格数值')">
					<input v-model="num" class="input" :placeholder="$t('请输入价格')"></input>
				</u-form-item>
				<!-- <u-form-item label="用户数量">
					<text class="input">12585</text>
				</u-form-item> -->
				<u-form-item :label="$t('投票后的平均值')">
					<text class="input">{{num2}}</text>
				</u-form-item>

			</u-form>
			<view class="btn-all" @click="MerchantAddConsensusPrice">{{$t("确认投票")}}</view>
		</view>
	</view>
</template>

<script>
	import {
		MerchantGetConsensusPrice,
		MerchantAddConsensusPrice
	} from '../../api/index.js'
	export default {
		data() {
			return {
				obj: [

				],
				jcb: 0,
				num: "",
				num2: "",
				time: "",
				timeStart: "",
				isShowTime: false,
				active: 1,
				config: [{
						key: 1,
						name: '节点数据'
					},
					// { key: 2, name: '计算器' },
					{
						key: 3,
						name: '共识价',
					},
				],
			}
		},
		onLoad() {
			this.MerchantGetConsensusPrice()
		},
		methods: {
			async MerchantGetConsensusPrice() {
				let res = await MerchantGetConsensusPrice()
				if (res.code == 1) {
					this.num2 = res.data
				}
			},
			async MerchantAddConsensusPrice() {
				let res = await MerchantAddConsensusPrice({
					value: this.num
				})
				if (res.code == 1) {
					this.num = ""
					uni.showToast({
						title: this.$t(res.msg)
					})
					this.MerchantGetConsensusPrice()
				}
			},
			getJcb() {
				if (this.getTime(this.time) < this.getTime("2019-03-14") || this.getTime(this.timeStart) < this.getTime(
						"2019-03-14")) {
					this.jcb = 0
					return
				}
				let obj = this.obj.filter(item => {
					console.log(this.getTime(item.time) < this.getTime(this.timeStart))
					if (this.getTime(item.time) < this.getTime(this.timeStart)) {
						return false
					} else {
						return true
					}
				})
				let num = 0
				while (obj.length) {
					if (obj[1]) {
						if (this.getTime(this.time) < this.getTime(obj[1].time)) {
							num += this.getItemTime(this.time, this.timeStart, obj[0].num)
							break;
						} else if (this.getTime(this.time) > this.getTime(obj[1].time)) {
							num += this.getItemTime(obj[1].time, obj[0].time, obj[0].num)
						}
					} else {
						num += this.getItemTime(this.time, this.timeStart, obj[0].num)
						break;
					}

					obj.splice(0, 1)
				}
				if (num) {
					this.jcb = num < 0 ? 0 : num
				}
			},
			getItemTime(satrtTime, endTime, num) {
				console.log(satrtTime, endTime, num)
				return (this.getTime(satrtTime) - this.getTime(endTime)) * num
			},
			getTime(time) {
				return new Date(time).getTime() / 3600000
			},
			tabChange(item) {
				this.active = item.key
			},
			changeTimeStart(e) {
				this.obj = [{
						time: "2019-03-14",
						num: 3.14
					},
					{
						time: "2019-05-23",
						num: 1.57
					},
					{
						time: "2019-07-11",
						num: 0.785
					},
					{
						time: "2019-11-01",
						num: 0.3925
					},
					{
						time: "2020-12-09",
						num: 0.19625
					},
					{
						time: "2022-03-01",
						num: 0.02367
					},
					{
						time: "2022-04-01",
						num: 0.02
					},
					{
						time: "2022-05-01",
						num: 0.019
					},
					{
						time: "2200-03-01",
						num: 0.0193
					}
				]
				this.timeStart = e.detail.value
				this.isShowTime2 = true
				this.getJcb()
			},
			changeTime(e) {
				this.obj = [{
						time: "2019-03-14",
						num: 3.14
					},
					{
						time: "2019-05-23",
						num: 1.57
					},
					{
						time: "2019-07-11",
						num: 0.785
					},
					{
						time: "2019-11-01",
						num: 0.3925
					},
					{
						time: "2020-12-09",
						num: 0.19625
					},
					{
						time: "2022-03-01",
						num: 0.02367
					},
					{
						time: "2022-04-01",
						num: 0.02
					},
					{
						time: "2022-05-01",
						num: 0.019
					},
					{
						time: "2200-03-01",
						num: 0.0193
					}
				]
				this.time = e.detail.value
				this.isShowTime = true
				this.getJcb()
			}
		}
	}
</script>

<style scoped>
	.container {
		padding: 0 30rpx;
		margin-top: -1px;
		min-height: calc(100vh - 44px);
	}

	.input {
		color: #F585FF;
		background-color: rgba(255, 255, 255, .3);
		width: 100%;
		padding: 20rpx;
		border-radius: 10rpx;
		margin-top: 10rpx;
		font-size: 28rpx
	}

	.iframe {
		width: 100%;
		height: 100%;
		background-color: #050523;
	}

	.iframe-wapper {
		position: fixed;
		width: 100vw;
		left: 0;
		bottom: 0;
		height: calc(94vh - 170rpx);
		overflow: hidden;
	}

	.btn-all {
		margin-top: 100rpx;
	}
</style>
