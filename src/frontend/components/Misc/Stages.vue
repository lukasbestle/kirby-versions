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

<style>
.lbvs-changes {
	font-family: var(--font-family-mono);
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
