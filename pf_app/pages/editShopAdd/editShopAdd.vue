<template>
	<view>
		<u-navbar :title="$t('添加商家')" bgColor="#14142B" leftIconColor="#F585FF" autoBack />
		<view class="my-page">
			<u-form labelPosition="top" labelWidth="400">
				<u-form-item :label="$t('商家名称(10字以内)')">
					<u-input v-model="form.name" color="#FFF"  :placeholder="$t('请输入商家名称')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('商家联系方式')">
					<u-input v-model="form.phone" color="#FFF"  :placeholder="$t('请输入商家联系方式')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('国家')">
					<u-input @focus="showPop" :disabled="false" :value="gj" color="#FFF"  :placeholder="$t('请选择国家')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('支付类型')">
					<u-input @focus="showPop" :value="zflx" color="#FFF"  :placeholder="$t('请选择支付类型')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('所在地区')">
					<u-input v-model="form.address" color="#FFF"  :placeholder="$t('请输入地区')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('详细地址')">
					<u-input @focus="changeAdd" v-model="address.name" color="#FFF"  :placeholder="$t('请选择详细地址')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('商家简介')">
					<u--textarea v-model="form.describe" color="#FFF" :placeholder="$t('请输入商品简介')"></u--textarea>
				</u-form-item>
			</u-form>
			<view class="pop-btn-wapper">
				<view @click="submit">{{$t('确认提交信息')}}</view>
			</view>
		</view>
		<u-popup :show="isShowPop" @close="close">
			<view class="pop-wapper">
				<view class="pop-title-wapper">
					<text>{{$t('全部筛选')}}</text>
				</view>
				<view class="pop-sub-title">
					{{$t('国家')}}
				</view>
				<view class="btn-wapper">
					<view @click="tabItem(index,'countryIndex')" :class="['btn-item',index==countryIndex?'btn-item-ac':'']" v-for="(item,index) in countryList" :key="item.id">
						{{item.name}}
					</view>
				</view>
				<view class="pop-sub-title">
					{{$t('商家支付类型')}}
				</view>
				<view class="btn-wapper">
					<view @click="tabItem(index,'merchantIndex')" :class="['btn-item',index==merchantIndex?'btn-item-ac':'']" v-for="(item,index) in merchantType" :key="item.id">
						{{$t(item.name)}}
					</view>
				</view>
				<view class="pop-btn-wapper">
					<view @click="close">{{$t('确认')}}</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import {MerchantUpMerchant} from '@/api/index.js'
	import { getMerchantType,getMerchantList,getCountryListApi,Merchant_addMerchant } from '@/api/index.js';
	export default {
		data() {
			return {
				id:"",
				form: {
					name:"",
					phone:"",
					address:"",
					describe:"",
					province:"--",
					city:"--",
					area:"--"
				},
				address:{},
				isShowPop:false,
				merchantType:[],
				merchantIndex:null,
				countryList:[],
				countryIndex:null,
			}
		},
		onLoad({id}) {
			this.id = id
		},
		computed:{
			zflx(){
				if(this.merchantIndex!=null){
					return this.merchantType[this.merchantIndex].name
				}
			},
			gj(){
				if(this.countryIndex!=null){
					return this.countryList[this.countryIndex].name
				}
			}
		},
		methods: {
			reset(){
				this.form = {
					name:"",
					phone:"",
					address:"",
					describe:"",
					province:"--",
					city:"--",
					area:"--"
				}
			},
			showPop(){
				this.getCountryListApi()
				this.getMerchantType()
				this.isShowPop = true
			},
			close(){
				this.isShowPop = false
			},
			tabItem(index,name){
				this[name] = index
			},
			changeAdd(){
				let _this = this
				uni.chooseLocation({
					success(res) {
						console.log(res)
						_this.address = res
					}
				})
			},
			//国家列表
			async getCountryListApi(){
				if(this.countryList.length) return
				let res = await getCountryListApi()
				if(res.code==1){
					this.countryList = res.data
				}
			},
			
			//支付类型列表
			async getMerchantType(){
				if(this.merchantType.length) return
				let res = await getMerchantType()
				if(res.code==1){
					this.merchantType = res.data
				}
			},
			async submit(){
				if(this.countryIndex==null||this.merchantIndex==null||!this.address.longitude){
					uni.showToast({
						icon:"none",
						title:this.$t("参数错误")
					})
					return
				}
				let res = await Merchant_addMerchant({
					...this.form,
					merchant_type_id:this.merchantType[this.merchantIndex].id,
					merchant_type_id:this.countryList[this.countryIndex].id,
					lat:this.address.latitude,
					lng:this.address.longitude
				})
				if(res.code==1){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					setTimeout(()=>{
						uni.navigateBack()
					},1000)
				}
			}
		}
	}
</script>

<style scoped>
	
	.pop-wapper{
		background-color: #232344;
		color: #CF21FE;
		padding: 0 20rpx;
	}
	.pop-title-wapper{
		text-align: center;
		padding: 20rpx 0;
	}
	.pop-sub-title{
		font-size: 28rpx;
		color: #FFF;
		margin: 20rpx 0;
	}
	.btn-wapper{
		display: flex;
		flex-wrap: wrap;
		// justify-content: space-between;
		width: calc(100% + 30rpx);
		max-height: 400rpx;
		overflow: hidden;
		overflow-y: scroll;
	}
	
	.btn-item{
		width: 226rpx;
		border: 1px solid #323256;
		background-color: #323256;
		color: #EEB6B1;
		border-radius: 10rpx;
		height: 64rpx;
		text-align: center;
		line-height: 64rpx;
		margin-bottom: 30rpx;
		margin-right: 10rpx;
		font-size: 28rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}	
	.btn-item-ac{
		width: 226rpx;
		border: 1px solid #cf21fe;
		color: #cf21fe;
		border-radius: 10rpx;
		height: 64rpx;
		text-align: center;
		line-height: 64rpx;
		margin-bottom: 30rpx;
		margin-right: 10rpx;
		font-size: 28rpx;
		background-color: none;
	}
	.pop-btn-wapper{
		background: #323256;
		display: flex;
		margin-bottom: 40rpx;
		height: 100rpx;
		border-radius: 10rpx;
		overflow: hidden;
	}
	.pop-btn-wapper view{
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 1;
		font-size: 28rpx;
	}
	.pop-btn-wapper view:last-child{
		background: linear-gradient(266deg,#e323ff 2%, #9041ff 92%);
		color: #FFF;
	}
	
	.my-page {
		padding: 0 30rpx;
	}

	.pop-btn-wapper {
		background: #323256;
		display: flex;
		margin-bottom: 40rpx;
		height: 100rpx;
		border-radius: 10rpx;
		overflow: hidden;
		margin-top: 50rpx;
	}

	.pop-btn-wapper view {
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 1;
		font-size: 28rpx;
		color: #FFF;
	}

	.pop-btn-wapper view:last-child {
		background: linear-gradient(266deg, #e323ff 2%, #9041ff 92%);
	}
</style>
