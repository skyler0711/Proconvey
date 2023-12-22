import { ComponentStory, ComponentMeta } from '@storybook/react'
import Table from './index'

export default {
  title: 'Components/Table',
  component: Table,
} as ComponentMeta<typeof Table>

export const PopulatedTable: ComponentStory<typeof Table> = () => {
  return (
    <Table>
      <Table.Head>
        <Table.Row>
          <Table.Cell as="th">Invoice</Table.Cell>
          <Table.Cell as="th">Amount</Table.Cell>
          <Table.Cell as="th">Packs</Table.Cell>
          <Table.Cell as="th">Date</Table.Cell>
        </Table.Row>
      </Table.Head>
      <Table.Body>
        <Table.Row>
          <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
          <Table.Cell>£250</Table.Cell>
          <Table.Cell>5</Table.Cell>
          <Table.Cell>22 Sepetember 2022</Table.Cell>
        </Table.Row>
        <Table.Row>
          <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
          <Table.Cell>£250</Table.Cell>
          <Table.Cell>5</Table.Cell>
          <Table.Cell>22 Sepetember 2022</Table.Cell>
        </Table.Row>
        <Table.Row>
          <Table.Cell>Conveyancer Plan - Sep 2022</Table.Cell>
          <Table.Cell>£250</Table.Cell>
          <Table.Cell>5</Table.Cell>
          <Table.Cell>22 Sepetember 2022</Table.Cell>
        </Table.Row>
      </Table.Body>
    </Table>
  )
}
