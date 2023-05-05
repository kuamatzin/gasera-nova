import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-color-picker', IndexField)
  app.component('detail-color-picker', DetailField)
  app.component('form-color-picker', FormField)
})
