<template>
	<div class="lbvs-version-label-cell">
		<lbvs-version :version="value" />
	</div>
</template>

<script>
export default {
	props: {
		value: Object
	}
};
</script>

<style>
.lbvs-version-label-cell {
	padding: 0.325rem 0.75rem;
}
</style>
