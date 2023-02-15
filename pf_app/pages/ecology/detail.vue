<template>
	<view class="ecology-detail container">
		<u-navbar :title="$t('ecology.detail')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<u-text margin="64rpx 0 16rpx 0" lines="1" size="48rpx" lineHeight="64rpx" color="#F585FF" :text="detail['title'+localeIndex]" blod></u-text>
		<view class="ecology-detail__info">
			<view class="desc">
				<view class="type">{{typeTag}}</view>
				<view class="count">{{$t('ecology.count')}}：{{detail.comment_number}}</view>
			</view>
			<u-rate :count="5" v-model="detail.star" size="38rpx" inactiveColor="rgba(255,255,255,0.6)" activeColor="#FFAA05" disabled></u-rate>
			<u-button @click="show = true" type="primary" style="margin-top: 40rpx;">{{$t('ecology.evaluate')}}</u-button>
		</view>
		<u-scroll-list class="ecology-detail__banner" :indicator="false">
			<u-image style="margin-right: 16rpx;" v-for="(item, index) in bannerList" width="240rpx" height="414rpx" :key="index" :src="baseUrl+item" radius="6rpx"></u-image>
		</u-scroll-list>
		<view class="ecology-detail__item">
			<view class="item-header">{{$t('ecology.recommender')}}</view>
			<u-text size="28rpx" lineHeight="40rpx" color="rgba(255, 255, 255, 0.8);" :text="detail['recommend'+localeIndex]" blod></u-text>
		</view>
		<view class="ecology-detail__item">
			<view class="item-header">{{$t('ecology.introduction')}}</view>
			<u-text size="28rpx" lineHeight="40rpx" color="rgba(255, 255, 255, 0.8);" :text="detail['introduce'+localeIndex]" blod></u-text>
		</view>
		<view class="ecology-detail__item">
			<view class="item-header">{{$t('ecology.developers')}}</view>
			<u-text size="28rpx" lineHeight="40rpx" color="rgba(255, 255, 255, 0.8);" :text="detail['developer'+localeIndex]" blod></u-text>
		</view>
		<view class="operate">
			<u-button :text="$t('ecology.download')" color="#323256" @click="operateHandler(detail.download_url)"></u-button>
			<u-button customStyle="margin-left: 36rpx" :text="$t('ecology.website')" color="#323256"@click="operateHandler(detail.url)"></u-button>
		</view>
		<view class="comment">
			<view class="comment-title">{{$t('article.comment')}}</view>
			<view class="comment-body">
				<comment-item v-for="item in list" :key="item.id" :data="item"></comment-item>
			</view>
			<navigator class="comment-all" :url="`/pages/article/comment?id=${id}&type=ecology1`">
				<view>
					{{$t('all')+' '}}{{$t('article.comment')}} ({{total}})
				</view>
				<u-icon style="margin-top: 4rpx;margin-left: 20rpx;" color="24rpx" name="arrow-right"></u-icon>
			</navigator>
		</view>
		<comment-bar :hideZan="true"  type="ecology" :sendComment="sendComment" :sendAction="newsSetCollect" :collect="detail.is_collect == 1" @success="init"></comment-bar>
		<u-toast ref="uToast"></u-toast>
		<u-popup :show="show" :round="10" mode="center" @close="close" @open="open">
			<view class="popTC">
				<view style="text-align: center; padding-bottom: 10px;color: #fff;">{{$t('ecology.evaluatetext')}}</view>
				<u-rate :count="5" v-model="stars" size="64rpx" inactiveColor="rgba(255,255,255,0.6)" activeColor="#FFAA05" @change="evaluates"></u-rate>
				<u-button :text="$t('ecology.score')" type="primary" size="small" @click="ratings" style="margin-top: 40rpx;"></u-button>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { baseUrl } from '@/api/http.js';
	import { getEcologyDetailApi,scores,newsSetCollect,newsAddComment,newsGetCommentList,news_cancelCollec } from '@/api/index.js';
	export default {
		filters: {
			
		},
		data() {
			return {
				total:0,
				baseUrl,
				id: '',
				detail: {},
				list:[],
				show: false,
				stars:0
			}
		},
		methods: {
			async sendComment(text){
				let res = await newsAddComment({
					ecology_id:this.id,
					content:text
				})
				if(res.code==1){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async newsSetCollect(){
				let api = this.detail.is_collect==1?news_cancelCollec:newsSetCollect
				console.log(news_cancelCollec)
				let res = await api({
					id:this.id
				})
				this.detail.is_collect = this.detail.is_collect==1?0:1
				this.$forceUpdate()
				uni.showToast({
					icon:'none',
					title:res.msg
				})
			},
			async getComment(){
				let res = await newsGetCommentList({page:1,listRows:12})
				if(res.code==1){
					this.list = res.data.data
					this.total = res.data.total
				}
			},
			
			init() {
				this.getComment()
				getEcologyDetailApi({ id: this.id }).then(({code, data, msg}) => {
					if(code == 1) this.detail = data;
				})
			},
			operateHandler(url) {
				if (url.includes('http://') || url.includes('https://')) {
					location.href = url;
				} else {
					location.href = `https://${url}`
				}
			},
			evaluates(val){
				this.stars = val
			},
			ratings(val){
				console.log(val)
				scores({ id: this.id,sco:this.stars }).then(({code, data, msg}) => {
					this.close()
					if(code == 1) {
						this.init()
						var obj = {type: 'success',
								message: msg,}
						this.showToast(obj)
					}else{
						var obj = {type: 'error',
								message: msg,}
						this.showToast(obj)
					}
				})
			},
			showToast(params) {
				this.$refs.uToast.show({
					...params,
				})
			},
			open() {
			  // console.log('open');
			},
			close() {
			  this.show = false
			  // console.log('close');
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
			typeTag() {
				let { type } = this.detail;
				const arr = [ 'official', 'unofficial', 'business', 'nonCommercial']
				console.log('ecology.'+arr[type - 1])
				return this.$t('ecology.'+arr[type - 1]);
			},
			bannerList() {
				let { images } = this.detail;
				return images? images.split(',') : [];
			}
		},
		onLoad(query) {
			let { id } = query;
			this.id = id;
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.comment {
		margin-top: 60rpx;
		padding-bottom: 48rpx;
		.comment-title {
			font-size: 32rpx;
			line-height: 40rpx;
			color: #F585FF;
		}
		.comment-body {
			padding-top: 32rpx;
		}
		.comment-all {
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 22rpx;
			text-align: center;
			color: #F585FF;
			background-color: rgba(245,133,255,0.3);
			border-radius: 8rpx;
		}
	}
	.ecology-detail {
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 240rpx;
		padding-top: 100rpx;
		.ecology-detail__info {
			.desc {
				margin-bottom: 16rpx;
				display: flex;
				align-content: center;
				.type {
					margin-right: 16rpx;
					padding: 4rpx 16rpx;
					font-size: 28rpx;
					line-height: 40rpx;
					color: #FFFFFF;
					background: linear-gradient(265deg, #E323FF 0%, #9041FF 100%);
					border-radius: 8rpx;
				}
				.count {
					font-size: 32rpx;
					line-height: 44rpx;
					color: rgba(255,255,255,0.8);
				}
			}
		}
		.ecology-detail__banner {
			margin-top: 24rpx;
			margin-bottom: 20rpx;
		}
		.ecology-detail__item {
			margin-bottom: 48rpx;
			.item-header {
				margin-bottom: 16rpx;
				font-size: 32rpx;
				line-height: 44rpx;
				color: #FFFFFF;
			}
		}
		.operate {
			display: flex;
		}
		.popTC{
			padding: 40upx;
			background-color:#050523 ;
			border-radius: 10px;
		}
	}
</style>
