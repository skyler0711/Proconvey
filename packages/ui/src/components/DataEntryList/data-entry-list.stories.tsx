import { ComponentStory, ComponentMeta } from '@storybook/react'
import Form from '../Form'
import DataEntryList from './index'

export default {
  title: 'Components/DataEntryList',
  component: DataEntryList,
} as ComponentMeta<typeof DataEntryList>

export const PopulatedList: ComponentStory<typeof DataEntryList> = () => {
  return (
    <DataEntryList>
      <DataEntryList.Head>
        <DataEntryList.Row>
          <DataEntryList.Cell as="th">Item</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Included</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Excluded</DataEntryList.Cell>
          <DataEntryList.Cell as="th">None</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Price</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Comments</DataEntryList.Cell>
        </DataEntryList.Row>
      </DataEntryList.Head>
      <DataEntryList.Body>
        <DataEntryList.Row>
          <DataEntryList.Cell>Bath</DataEntryList.Cell>
          <Form.Checkbox.Group>
            {
              ({ selected, onChange }) => (
                <>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="1" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="2" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="3" rounded={true} /></DataEntryList.Cell>
                </>
              )
            }
          </Form.Checkbox.Group>
          <DataEntryList.Cell>
            <Form.Input placeholder="e.g. £3500" />
          </DataEntryList.Cell>
          <DataEntryList.Cell>
            <Form.Input placeholder="Enter your comment" />
          </DataEntryList.Cell>
        </DataEntryList.Row>
        <DataEntryList.Row>
          <DataEntryList.Cell>Bath</DataEntryList.Cell>
          <Form.Checkbox.Group>
            {
              ({ selected, onChange }) => (
                <>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="1" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="2" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="3" rounded={true} /></DataEntryList.Cell>
                </>
              )
            }
          </Form.Checkbox.Group>
          <DataEntryList.Cell>
            <Form.Input placeholder="e.g. £3500" />
          </DataEntryList.Cell>
          <DataEntryList.Cell>
            <Form.Input placeholder="Enter your comment" />
          </DataEntryList.Cell>
        </DataEntryList.Row>
        <DataEntryList.Row>
          <DataEntryList.Cell>Bath</DataEntryList.Cell>
          <Form.Checkbox.Group>
            {
              ({ selected, onChange }) => (
                <>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="1" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="2" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="3" rounded={true} /></DataEntryList.Cell>
                </>
              )
            }
          </Form.Checkbox.Group>
          <DataEntryList.Cell>
            <Form.Input placeholder="e.g. £3500" />
          </DataEntryList.Cell>
          <DataEntryList.Cell>
            <Form.Input placeholder="Enter your comment" />
          </DataEntryList.Cell>
        </DataEntryList.Row>
      </DataEntryList.Body>
    </DataEntryList>
  )
}

export const ErrorList: ComponentStory<typeof DataEntryList> = () => {
  return (
    <DataEntryList>
      <DataEntryList.Head>
        <DataEntryList.Row>
          <DataEntryList.Cell as="th">Item</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Included</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Excluded</DataEntryList.Cell>
          <DataEntryList.Cell as="th">None</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Price</DataEntryList.Cell>
          <DataEntryList.Cell as="th">Comments</DataEntryList.Cell>
        </DataEntryList.Row>
      </DataEntryList.Head>
      <DataEntryList.Body>
        <DataEntryList.Row>
          <DataEntryList.Cell>Bath</DataEntryList.Cell>
          <Form.Checkbox.Group>
            {
              ({ selected, onChange }) => (
                <>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="1" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="2" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="3" rounded={true} /></DataEntryList.Cell>
                </>
              )
            }
          </Form.Checkbox.Group>
          <DataEntryList.Cell>
            <Form.Input placeholder="e.g. £3500" />
          </DataEntryList.Cell>
          <DataEntryList.Cell>
            <Form.Input placeholder="Enter your comment" />
          </DataEntryList.Cell>
        </DataEntryList.Row>
        <DataEntryList.Row>
          <DataEntryList.Cell hasError>Bath</DataEntryList.Cell>
          <Form.Checkbox.Group>
            {
              ({ selected, onChange }) => (
                <>
                  <DataEntryList.Cell hasError><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="1" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell hasError><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="2" rounded={true} /></DataEntryList.Cell>
                  <DataEntryList.Cell hasError><Form.Checkbox name="bath" selected={selected} onChange={onChange} value="3" rounded={true} /></DataEntryList.Cell>
                </>
              )
            }
          </Form.Checkbox.Group>
          <DataEntryList.Cell hasError>
            <Form.Input placeholder="e.g. £3500" />
          </DataEntryList.Cell>
          <DataEntryList.Cell hasError>
            <Form.Input placeholder="Enter your comment" />
          </DataEntryList.Cell>
        </DataEntryList.Row>
      </DataEntryList.Body>
    </DataEntryList>
  )
}
