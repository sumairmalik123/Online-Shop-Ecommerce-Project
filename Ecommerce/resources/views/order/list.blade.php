@extends('layouts/userlayout/app')
<!--content section-->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Orders</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('categories.create')}}" class="btn btn-primary">New Category</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            {{ Session::get('success') }}
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible __web-inspector-hide-shortcut__">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4> {{ Session::get('error') }}
        </div>
        @endif
        <form action="" method="get">
        <div class="card">
            <div class="card-header">
                <div class="card-heading" style="display: inline-block;" >
                    <button type="button" onclick="window.location.href='{{route('order.list')}}'" class="btn btn-primary btn-sm">Return</button>
                </div>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 250px;">
                        <input for="keyword" type="text" value="{{ Request::get('keyword') }}" name="keyword" id="search" class="form-control float-right" placeholder="Search">
    
                        <div class="input-group-append">
                          <button id="keyword" type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                          </button>
                        </div>
                      </div>
                </div>
            </form>
            </div>
            <div class="card-body table-responsive p-0">								
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="60">Order#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($orders->isNotEmpty())
                        @foreach ($orders as $order)
                        <tr>
                            <td><a href="{{ route('order.detail',$order->id) }}">{{ $order->id }}</a></td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->email }}</td>
                            <td>{{ $order->mobile }}</td>
                            <td>
                            @if ($order->status == 'pending')
                             <span class="badge bg-danger">Pending</span>
                            @elseif ($order->status == 'shipped')
                            <span class="badge bg-info">Shipped</span>
                            @elseif ($order->status == 'delivered')
                            <span class="badge bg-success">Delivered</span>
                            @elseif ($order->status == 'cancelled')
                            <span class="badge bg-warning">Cancelled</span>   
                            @endif
                            </td>
                            <td>{{ number_format($order->grand_total,2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M,Y') }}</td>
                           
                        </tr>
                        @endforeach
                            
                        @else
                            <tr>
                                <td colspan="5">No category found.</td>
                            </tr>
                        @endif     
                    </tbody>
                </table>										
            </div>
            <div class="card-footer clearfix">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection
<!--custom js section-->
@section('customjs')

@endsection