<template>
	<ul class="lbvs-changes">
		<li v-for="(status, path) in changes" :key="path">
			<div>
				<k-input :id="path" :name="path" :value="status" :checked="status !== false" type="checkbox" @input="($event) => updateChange(path, status, $event)"/>
			</div>
			<span
				:data-status="status"
				:title="$t('versions.label.status.' + status)"
			>
				{{ status }}
			</span>
			<label :for="path">{{ path }}</label>
		</li>
	</ul>
</template>

<script>
export default {
	props: {
		changes: Object
	},

	computed: {
		markedChangesForStaging () {
			return this.$store.state.versions.data.markedChangesForStaging;
		}
	},

	mounted () {
		for (const change in this.changes) {
			this.$store.dispatch('versions/setChanges', {
				path: change,
				status: this.changes[change]
			});
		}
	},

	methods: {
		updateChange (path, state, checked) {
			this.$store.dispatch('versions/setChanges', {
				path: path,
				status: checked ? state : false
			});
		}
	}
};
</script>

<style>
.lbvs-changes li {
	padding-inline-start: 1.2em;
	position: relative;
	display: flex;
}

.lbvs-changes li > span {
	position: absolute;
	inset-inline-start: 0;

	font-family: var(--font-mono);
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
