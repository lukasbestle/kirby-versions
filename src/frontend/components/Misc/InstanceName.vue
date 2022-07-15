<template>
	<component
		:is="element"
		:style="{ backgroundColor: instance.color }"
		class="lbvs-instance-name"
	>
		{{ instance.name }}
	</component>
</template>

<script>
export default {
	props: {
		inline: Boolean,
		instance: Object
	},
	computed: {
		element() {
			return this.inline ? "span" : "strong";
		}
	}
};
</script>

<style>
.lbvs-instance-name {
	display: inline-block;
	padding: 0 0.3em;

	border-radius: var(--rounded);
	color: var(--color-text);
}
strong.lbvs-instance-name {
	padding: 0.2em 0.4em;
}
</style>
