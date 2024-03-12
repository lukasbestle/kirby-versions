<template>
	<ul class="lbvs-changes">
		<li v-for="(status, path) in toStage" :key="path">
			<span
				:data-status="status"
				:title="$t('versions.label.status.' + status)"
			>
				{{ status }}
			</span>
			{{ path }}
		</li>
	</ul>
</template>

<script>
export default {
	props: {
		toStage: Object
	}
};
</script>

<style lang="scss">
.lbvs-changes {
	font-family: var(--font-family-mono);
	display: flex;
	flex-direction: column;
	gap: 4px;
	max-height: 45svh;
	min-height: 400px;
	overflow: auto;

	.k-input {
		min-height: unset;
		margin: 0 7px 0 0;
		background: none;
		outline: none;
		border: none;
	}

	label {
		line-height: 1.2;
	}
}

.lbvs-changes li {
	padding-inline-start: 1.2em;
	position: relative;
	display: flex;
}

.lbvs-changes li > span {
	position: absolute;
	inset-inline-start: 0;

	font-weight: bold;
}

.lbvs-changes li span[data-status="+"],
.lbvs-changes li span[data-status="C"] {
	color: var(--color-positive);
}

.lbvs-changes li span[data-status="-"] {
	color: var(--color-negative);
}

.lbvs-changes li span[data-status="M"],
.lbvs-changes li span[data-status="R"] {
	color: var(--color-notice);
}
</style>
