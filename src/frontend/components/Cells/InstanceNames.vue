<template>
	<div class="lbvs-instance-names-cell">
		<lbvs-instance-name
			v-for="instance in instances"
			:key="instance"
			:inline="true"
			:instance="$store.state.versions.data.instances[instance]"
		/>
	</div>
</template>

<script>
export default {
	props: {
		// support both a list of instances or a single instance
		value: [Array, Object]
	},
	computed: {
		instances() {
			// wrap a single instance in an array for the loop
			return Array.isArray(this.value) ? this.value : [this.value];
		}
	}
};
</script>

<style>
.lbvs-instance-names-cell {
	line-height: 1.5;
	padding: 0.325rem 0.75rem;
}

.lbvs-instance-names-cell .lbvs-instance-name {
	margin-block: 0.1625rem;
	margin-inline-end: 0.325rem;
}
</style>
