import { ComponentStory, ComponentMeta } from '@storybook/react'
import Upload from './index'

export default {
  title: 'Components/Forms/Upload',
  component: Upload,
} as ComponentMeta<typeof Upload>

const Template: ComponentStory<typeof Upload> = (args) => <Upload {...args} />

export const FileUpload: ComponentStory<typeof Upload> = Template.bind({})
FileUpload.args = {
  label: 'Upload Document',
  name: 'file',
}

export const ErrorFileUpload: ComponentStory<typeof Upload> = Template.bind({})
ErrorFileUpload.args = {
  label: 'Upload Document',
  name: 'file',
  error: 'Error message',
}
