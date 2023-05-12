import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-boolean-switcher', IndexField)
  app.component('detail-boolean-switcher', DetailField)
  app.component('form-boolean-switcher', FormField)
})
