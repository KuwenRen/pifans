<template>
	<u-navbar :title="title" @leftClick="leftClick" :bgColor="bgColor" :autoBack="true"> </u-navbar>
</template>

<script>
	export default {
		name:"navbar",
		props: {
			title: String
		},
		data() {
			return {
				bgColor: '#14142B'
			};
		},
		methods: {
			leftClick () {
				uni.navigateBack({
					delta:1
				});
			}
		}
	}
</script>

<style>
	

</style>
