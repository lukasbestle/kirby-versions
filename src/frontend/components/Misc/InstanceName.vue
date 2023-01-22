<template>
	<component
		:is="element"
		:style="{ backgroundColor: instanceObj.color }"
		class="lbvs-instance-name"
	>
		{{ instanceObj.name }}
	</component>
</template>

<script>
export default {
	props: {
		inline: Boolean,

		// support both a full instance object or just the name string
		instance: [Object, String]
	},
	computed: {
		element() {
			return this.inline ? "span" : "strong";
		},
		instanceObj() {
			if (typeof this.instance === "string") {
				// no color known, use a faded out background
				return { name: this.instance, color: "var(--color-gray-300)" };
			}

			// already an object
			return this.instance;
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
