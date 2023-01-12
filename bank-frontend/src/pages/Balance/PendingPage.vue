<template>
  <q-page>
    <TransactionPendingComponent />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import TransactionPendingComponent from 'src/components/Balance/Transaction/TransactionPendingComponent.vue';

export default defineComponent({
  name: 'PendingPage',

  components: {
    TransactionPendingComponent,
  },
});
</script>
