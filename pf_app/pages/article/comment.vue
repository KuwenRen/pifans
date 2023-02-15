<template>
	<u-list class="comment container" @scrolltolower="getData">
		<u-navbar :title="titleComputed" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="sy-header">
			<view class="sy-header__inner">{{$t('all') +' '}}{{$t('article.comment')}} ({{count}}) </view>
		</view>
		<view class="comment-list" v-if="list.length !== 0">
			<comment-item v-for="item in list" :key="item.id" :data="item" />
			<sy-loadmore v-if="status !== 'none'" :status="status" />
		</view>
		<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')" />
		<comment-bar v-if="type!='shop'&&type!='ecology1'" :id="detail.id" :type="type" :give="detail.is_give == 1" :collect="detail.is_collect == 1" @success="init"></comment-bar>
		<view v-if="type!='ecology1'" class="btn-all" @click="toComment">{{$t("去评价")}}</view>
	</u-list>
</template>

<script>
	import { getCommentListApi, getArticleDetailApi ,MerchantMerchantLog,newsGetCommentList} from '@/api/index.js'
	export default {
		data() {
			return {
				id: '',
				type: '',
				detail: {},
				status: 'none',
				count: 0,
				list: [],
				page: 0
			}
		},
		methods: {
			toComment(){
				uni.navigateTo({
					url:`/pages/commentShop/commentShop?id=${this.id}`
				})
			},
			getListHandler() {
				let api = this.type=="ecology1"?newsGetCommentList:getCommentListApi
				let { id, type, list, page } = this;
				(this.status !== 'nomore') && api({ id, type: type == 'Information'? 1 : 2, page: page + 1, listRows: 12 }).then(({code, data:{ data, total }, msg}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
						}
						this.count = total;
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			async getListShop(){
				if(this.page == 0){
					this.page = 1
				}
				let { id, type, list, page } = this;
				if(this.status == 'nomore') return
				let res = await MerchantMerchantLog({id,page,limit:10})
				if (res.code == 1) {
					if (res.data.data.length == 0) {
						this.status = 'nomore';
					} else {
						this.page = page + 1;
						res.data.data = res.data.data.map(item=>{
							item.user = item.users
							return item
						})
						this.list = list.concat(res.data.data);
					}
					this.count = res.data.total;
				} 
				
				
			},
			getArticleDetail() {
				getArticleDetailApi(this.type, { id: this.id, type: 0 }).then(({code, data, msg}) => {
					if (code == 1) {
						this.detail = data
					} else {
						msg && uni.$u.toast(msg);
					}
				})
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				if(this.type=="shop"){
					this.getListShop()
				}else{
					this.getListHandler();
					if(this.type!='ecology1'){
						this.getArticleDetail();
					}
				}
				
			},
			getData(){
				if(this.type=="shop"){
					this.getListShop()
				}else{
					this.getListHandler()
				}
			}
		},
		computed: {
			titleComputed() {
				return this.$t('all')+' '+this.$t('article.comment');
			}
		},
		onLoad({ id, type }) {
			console.log('11111')
			this.id = id;
			this.type = type;
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.comment {
		padding-top: 170rpx;
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 140rpx;
		box-sizing: border-box;
	}
	.btn-all{
		position: fixed;
		bottom: 30rpx;
		width: 630rpx;
	}
</style>
