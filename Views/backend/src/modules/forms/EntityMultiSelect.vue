<template>
    <div class="entity-multi-select select" :class="{ focus: isFocus }" @click="focus()">
        <div class="selected-value row">
            <template v-if="selectedValue">
                <span class="flex">
                    {{ selectedValue }}
                </span>
            </template>
            <span v-else class="nothing-selected flex">
                <slot name="nothing-selected">(nothing selected)</slot>
            </span>
            <div class="arrow">
                <fa icon="angle-down" v-if="!isFocus" />
                <fa icon="angle-up" v-else />
            </div>
        </div>
        <div class="dropdown">
            <v-input class="filter" v-model="filter" type="text" placeholder="Search..." ref="input" />

            <ul class="values" v-if="items.length > 0" ref="list">
                <li v-for="item in items"
                    :class="{ active: isActive(item) > -1, disabled: item._disabled === true }"
                    @click="select(item)" ref="items">
                    <slot name="item" :item="item" :displayField="displayField">
                        {{ item[displayField] }}
                    </slot>
                </li>
            </ul>
            <span v-else class="nothing-found">
                Nothing found for <span class="filter-term">"{{ filter }}"</span>
            </span>
        </div>
    </div>
</template>

<script>
export default {
  name: 'v-entity-multi-select',
  description: 'A custom styled select field with search input.',
  props: {
    data: {
      type: Array,
      required: true,
      description: 'The data which should be shown.',
      default: []
    },
    displayField: {
      type: String,
      required: false,
      description: 'The underlying data field name to bind to this selectfield.',
      default: 'label'
    },
    valueField: {
      type: String,
      required: false,
      description: 'The underlying data field name to bind to this selectfield.',
      default: 'id'
    },
    value: {
      type: Array,
      required: true,
      description: 'The associated value.'
    }
  },
  computed: {
    selectedIndex() {
      return this.items.findIndex(item => item.id === this.value)
    },
    selectedValue() {
      return this.value.length > 0
        ? this.value.map(item => item[this.displayField]).join(', ')
        : null
    },
    items() {
      let me = this

      if (!me.filter) {
        return me.data
      }

      return me.data.filter(item => item[me.displayField].toLowerCase().indexOf(me.filter.toLowerCase()) > -1)
    }
  },
  data: () => ({
    filter: '',
    isFocus: false,
    preventUnfocus: false,
    preventFocus: false
  }),
  watch: {
    filter() {
      let me = this

      me.$nextTick(() => {
        me.ensureItemVisibility()
      })
    }
  },
  beforeMount() {
    let me = this

    document.addEventListener('click', me.onBodyClick.bind(me))
  },
  beforeDestroy() {
    let me = this

    document.removeEventListener('click', me.onBodyClick.bind(me))
  },
  methods: {
    isActive(item) {
      return this.value.findIndex(i => parseInt(i[this.valueField]) === parseInt(item[this.valueField]))
    },
    select(item, unfocus) {
      let me = this

      if (item._disabled === true) {
        return
      }

      if (typeof unfocus === 'undefined') {
        unfocus = false
      }

      let index = me.isActive(item)

      if (index > -1) {
        me.value.splice(index, 1)
      } else {
        me.value.push(item)
      }

      me.$emit('input', me.value)

      if (unfocus) {
        me.isFocus = false
        me.preventFocus = true
      } else {
        me.$nextTick(() => {
          me.ensureItemVisibility()
        })
      }
    },
    focus() {
      let me = this

      if (me.preventFocus) {
        me.preventFocus = false
        return
      }

      me.preventUnfocus = true
      me.isFocus = true

      me.$nextTick(() => {
        me.$refs.input.focus()
      })
    },
    onBodyClick() {
      let me = this

      if (me.preventUnfocus) {
        me.preventUnfocus = false
        return
      }

      me.isFocus = false
    },
    onKeyDown(e) {
      let me = this

      if (me.isFocus === false) {
        return
      }

      if (e.keyCode === 27) { // esc
        if (me.filter.length > 0) { // first ESC clears the search box
          me.filter = ''
        } else { // second ESC closes the selectbox
          me.isFocus = false
        }
      }
    },
    ensureItemVisibility() {
      let me = this

      // scroll to selected item
      let _list = me.$refs.list
      let _items = me.$refs.items
      let _item = _items.find(i => i.classList.contains('active'))

      if (_item) {
        // viewport
        let offsetTop = _item.offsetTop
        let height = _item.offsetHeight

        // make sure the selected item is visible
        _list.scrollTop = offsetTop + (height * 3) - _list.offsetHeight
      }
    }
  }
}
</script>
