<template>
  <q-page>
    <div class="q-pa-md bg-cyan text-white">
      <DateSelectorComponent @dateSelectedChange="dateSelectedChange" />
    </div>
    <div class="q-gutter-y-md" style="max-width: 600px">
      <q-tabs v-model="tab" indicator-color="purple" class="text-teal">
        <q-tab name="PENDING" label="PENDING" />
        <q-tab name="ACCEPTED" label="ACCEPTED" />
        <q-tab name="REJECTED" label="REJECTED" />
      </q-tabs>
      <q-separator />
      <q-tab-panels v-model="tab">
        <q-tab-panel name="PENDING">
          <TransactionListComponent
            transaction-status="P"
            :date-option="dateOption"
          />
        </q-tab-panel>
        <q-tab-panel name="ACCEPTED">
          <TransactionListComponent
            transaction-status="A"
            :date-option="dateOption"
          />
          <div class="fixed-bottom-right" style="margin: 15px">
            <q-btn
              round
              type="button"
              color="primary"
              icon="add"
              :size="'lg'"
              @click="goToDeposit"
            />
          </div>
        </q-tab-panel>
        <q-tab-panel name="REJECTED">
          <TransactionListComponent
            transaction-status="R"
            :date-option="dateOption"
          />
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import DateSelectorComponent from 'src/components/Tools/DateSelectorComponent.vue';
import TransactionListComponent from 'src/components/Balance/Transaction/TransactionListComponent.vue';
import router from 'src/router';
import { IOption } from 'src/models/shared/baseinterfaces';
import { useConfigStore } from 'src/stores/config';
export default defineComponent({
  name: 'ChecksPage',

  components: {
    DateSelectorComponent,
    TransactionListComponent,
  },

  setup() {
    const configstore = useConfigStore();

    const balanceValue = ref(200);
    const tab = ref('ACCEPTED');

    const dateOption = ref(configstore.getCurrentMonthQuery);

    function dateSelectedChange(selectedOption: IOption) {
      dateOption.value = selectedOption.option as string;
    }

    function goToDeposit() {
      router.push({ name: 'deposit' });
    }

    return {
      balanceValue,
      tab,
      goToDeposit,
      dateSelectedChange,
      dateOption,
    };
  },
});
</script>
